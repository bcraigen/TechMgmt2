<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.Network.Email
 * @since         CakePHP(tm) v 2.0.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Multibyte', 'I18n');
App::uses('AbstractTransport', 'Network/Email');
App::uses('File', 'Utility');
App::uses('String', 'Utility');
App::uses('View', 'View');

/**
 * CakePHP email class.
 *
 * This class is used for handling Internet Message Format based
 * based on the standard outlined in http://www.rfc-editor.org/rfc/rfc2822.txt
 *
 * @package       Cake.Network.Email
 */
class CakeEmail {

/**
 * Default X-Mailer
 *
 * @var string
 */
	const EMAIL_CLIENT = 'CakePHP Email';

/**
 * Line length - no should more - RFC 2822 - 2.1.1
 *
 * @var int
 */
	const LINE_LENGTH_SHOULD = 78;

/**
 * Line length - no must more - RFC 2822 - 2.1.1
 *
 * @var int
 */
	const LINE_LENGTH_MUST = 998;

/**
 * Type of message - HTML
 *
 * @var string
 */
	const MESSAGE_HTML = 'html';

/**
 * Type of message - TEXT
 *
 * @var string
 */
	const MESSAGE_TEXT = 'text';

/**
 * Holds the regex pattern for email validation
 *
 * @var string
 */
	const EMAIL_PATTERN = '/^((?:[\p{L}0-9.!#$%&\'*+\/=?^_`{|}~-]+)*@[\p{L}0-9-.]+)$/ui';

/**
 * Recipient of the email
 *
 * @var array
 */
	protected $_to = array();

/**
 * The mail which the email is sent from
 *
 * @var array
 */
	protected $_from = array();

/**
 * The sender email
 *
 * @var array
 */
	protected $_sender = array();

/**
 * The email the recipient will reply to
 *
 * @var array
 */
	protected $_replyTo = array();

/**
 * The read receipt email
 *
 * @var array
 */
	protected $_readReceipt = array();

/**
 * The mail that will be used in case of any errors like
 * - Remote mailserver down
 * - Remote user has exceeded his quota
 * - Unknown user
 *
 * @var array
 */
	protected $_returnPath = array();

/**
 * Carbon Copy
 *
 * List of email's that should receive a copy of the email.
 * The Recipient WILL be able to see this list
 *
 * @var array
 */
	protected $_cc = array();

/**
 * Blind Carbon Copy
 *
 * List of email's that should receive a copy of the email.
 * The Recipient WILL NOT be able to see this list
 *
 * @var array
 */
	protected $_bcc = array();

/**
 * Message ID
 *
 * @var bool|string
 */
	protected $_messageId = true;

/**
 * Domain for messageId generation.
 * Needs to be manually set for CLI mailing as env('HTTP_HOST') is empty
 *
 * @var string
 */
	protected $_domain = null;

/**
 * The subject of the email
 *
 * @var string
 */
	protected $_subject = '';

/**
 * Associative array of a user defined headers
 * Keys will be prefixed 'X-' as per RFC2822 Section 4.7.5
 *
 * @var array
 */
	protected $_headers = array();

/**
 * Layout for the View
 *
 * @var string
 */
	protected $_layout = 'default';

/**
 * Template for the view
 *
 * @var string
 */
	protected $_template = '';

/**
 * View for render
 *
 * @var string
 */
	protected $_viewRender = 'View';

/**
 * Vars to sent to render
 *
 * @var array
 */
	protected $_viewVars = array();

/**
 * Theme for the View
 *
 * @var array
 */
	protected $_theme = null;

/**
 * Helpers to be used in the render
 *
 * @var array
 */
	protected $_helpers = array('Html');

/**
 * Text message
 *
 * @var string
 */
	protected $_textMessage = '';

/**
 * Html message
 *
 * @var string
 */
	protected $_htmlMessage = '';

/**
 * Final message to send
 *
 * @var array
 */
	protected $_message = array();

/**
 * Available formats to be sent.
 *
 * @var array
 */
	protected $_emailFormatAvailable = array('text', 'html', 'both');

/**
 * What format should the email be sent in
 *
 * @var string
 */
	protected $_emailFormat = 'text';

/**
 * What method should the email be sent
 *
 * @var string
 */
	protected $_transportName = 'Mail';

/**
 * Instance of transport class
 *
 * @var AbstractTransport
 */
	protected $_transportClass = null;

/**
 * Charset the email body is sent in
 *
 * @var string
 */
	public $charset = 'utf-8';

/**
 * Charset the email header is sent in
 * If null, the $charset property will be used as default
 *
 * @var string
 */
	public $headerCharset = null;

/**
 * The application wide charset, used to encode headers and body
 *
 * @var string
 */
	protected $_appCharset = null;

/**
 * List of files that should be attached to the email.
 *
 * Only absolute paths
 *
 * @var array
 */
	protected $_attachments = array();

/**
 * If set, boundary to use for multipart mime messages
 *
 * @var string
 */
	protected $_boundary = null;

/**
 * Configuration to transport
 *
 * @var string|array
 */
	protected $_config = array();

/**
 * 8Bit character sets
 *
 * @var array
 */
	protected $_charset8bit = array('UTF-8', 'SHIFT_JIS');

/**
 * Define Content-Type charset name
 *
 * @var array
 */
	protected $_contentTypeCharset = array(
		'ISO-2022-JP-MS' => 'ISO-2022-JP'
	);

/**
 * Regex for email validation
 *
 * If null, filter_var() will be used. Use the emailPattern() method
 * to set a custom pattern.'
 *
 * @var string
 */
	protected $_emailPattern = self::EMAIL_PATTERN;

/**
 * The class name used for email configuration.
 *
 * @var string
 */
	protected $_configClass = 'EmailConfig';

/**
 * Constructor
 *
 * @param array|string $config Array of configs, or string to load configs from email.php
 */
	public function __construct($config = null) {
		$this->_appCharset = Configure::read('App.encoding');
		if ($this->_appCharset !== null) {
			$this->charset = $this->_appCharset;
		}
		$this->_domain = preg_replace('/\:\d+$/', '', env('HTTP_HOST'));
		if (empty($this->_domain)) {
			$this->_domain = php_uname('n');
		}

		if ($config) {
			$this->config($config);
		}
		if (empty($this->headerCharset)) {
			$this->headerCharset = $this->charset;
		}
	}

/**
 * From
 *
 * @param string|array $email Null to get, String with email,
 *   Array with email as key, name as value or email as value (without name)
 * @param string $name Name
 * @return array|CakeEmail
 * @throws SocketException
 */
	public function from($email = null, $name = null) {
		if ($email === null) {
			return $this->_from;
		}
		return $this->_setEmailSingle('_from', $email, $name, __d('cake_dev', 'From requires only 1 email address.'));
	}

/**
 * Sender
 *
 * @param string|array $email Null to get, String with email,
 *   Array with email as key, name as value or email as value (without name)
 * @param string $name Name
 * @return array|CakeEmail
 * @throws SocketException
 */
	public function sender($email = null, $name = null) {
		if ($email === null) {
			return $this->_sender;
		}
		return $this->_setEmailSingle('_sender', $email, $name, __d('cake_dev', 'Sender requires only 1 email address.'));
	}

/**
 * Reply-To
 *
 * @param string|array $email Null to get, String with email,
 *   Array with email as key, name as value or email as value (without name)
 * @param string $name Name
 * @return array|CakeEmail
 * @throws SocketException
 */
	public function replyTo($email = null, $name = null) {
		if ($email === null) {
			return $this->_replyTo;
		}
		return $this->_setEmailSingle('_replyTo', $email, $name, __d('cake_dev', 'Reply-To requires only 1 email address.'));
	}

/**
 * Read Receipt (Disposition-Notification-To header)
 *
 * @param string|array $email Null to get, String with email,
 *   Array with email as key, name as value or email as value (without name)
 * @param string $name Name
 * @return array|CakeEmail
 * @throws SocketException
 */
	public function readReceipt($email = null, $name = null) {
		if ($email === null) {
			return $this->_readReceipt;
		}
		return $this->_setEmailSingle('_readReceipt', $email, $name, __d('cake_dev', 'Disposition-Notification-To requires only 1 email address.'));
	}

/**
 * Return Path
 *
 * @param string|array $email Null to get, String with email,
 *   Array with email as key, name as value or email as value (without name)
 * @param string $name Name
 * @return array|CakeEmail
 * @throws SocketException
 */
	public function returnPath($email = null, $name = null) {
		if ($email === null) {
			return $this->_returnPath;
		}
		return $this->_setEmailSingle('_returnPath', $email, $name, __d('cake_dev', 'Return-Path requires only 1 email address.'));
	}

/**
 * To
 *
 * @param string|array $email Null to get, String with email,
 *   Array with email as key, name as value or email as value (without name)
 * @param string $name Name
 * @return array|CakeEmail
 */
	public function to($email = null, $name = null) {
		if ($email === null) {
			return $this->_to;
		}
		return $this->_setEmail('_to', $email, $name);
	}

/**
 * Add To
 *
 * @param string|array $email Null to get, String with email,
 *   Array with email as key, name as value or email as value (without name)
 * @param string $name Name
 * @return $this
 */
	public function addTo($email, $name = null) {
		return $this->_addEmail('_to', $email, $name);
	}

/**
 * Cc
 *
 * @param string|array $email Null to get, String with email,
 *   Array with email as key, name as value or email as value (without name)
 * @param string $name Name
 * @return array|CakeEmail
 */
	public function cc($email = null, $name = null) {
		if ($email === null) {
			return $this->_cc;
		}
		return $this->_setEmail('_cc', $email, $name);
	}

/**
 * Add Cc
 *
 * @param string|array $email Null to get, String with email,
 *   Array with email as key, name as value or email as value (without name)
 * @param string $name Name
 * @return $this
 */
	public function addCc($email, $name = null) {
		return $this->_addEmail('_cc', $email, $name);
	}

/**
 * Bcc
 *
 * @param string|array $email Null to get, String with email,
 *   Array with email as key, name as value or email as value (without name)
 * @param string $name Name
 * @return array|CakeEmail
 */
	public function bcc($email = null, $name = null) {
		if ($email === null) {
			return $this->_bcc;
		}
		return $this->_setEmail('_bcc', $email, $name);
	}

/**
 * Add Bcc
 *
 * @param string|array $email Null to get, String with email,
 *   Array with email as key, name as value or email as value (without name)
 * @param string $name Name
 * @return $this
 */
	public function addBcc($email, $name = null) {
		return $this->_addEmail('_bcc', $email, $name);
	}

/**
 * Charset setter/getter
 *
 * @param string $charset Character set.
 * @return string this->charset
 */
	public function charset($charset = null) {
		if ($charset === null) {
			return $this->charset;
		}
		$this->charset = $charset;
		if (empty($this->headerCharset)) {
			$this->headerCharset = $charset;
		}
		return $this->charset;
	}

/**
 * HeaderCharset setter/getter
 *
 * @param string $charset Character set.
 * @return string this->charset
 */
	public function headerCharset($charset = null) {
		if ($charset === null) {
			return $this->headerCharset;
		}
		return $this->headerCharset = $charset;
	}

/**
 * EmailPattern setter/getter
 *
 * @param string|bool|null $regex The pattern to use for email address validation,
 *   null to unset the pattern and make use of filter_var() instead, false or
 *   nothing to return the current value
 * @return string|$this
 */
	public function emailPattern($regex = false) {
		if ($regex === false) {
			return $this->_emailPattern;
		}
		$this->_emailPattern = $regex;
		return $this;
	}

/**
 * Set email
 *
 * @param string $varName Property name
 * @param string|array $email String with email,
 *   Array with email as key, name as value or email as value (without name)
 * @param string $name Name
 * @return $this
 */
	protected function _setEmail($varName, $email, $name) {
		if (!is_array($email)) {
			$this->_validateEmail($email);
			if ($name === null) {
				$name = $email;
			}
			$this->{$varName} = array($email => $name);
			return $this;
		}
		$list = array();
		foreach ($email as $key => $value) {
			if (is_int($key)) {
				$key = $value;
			}
			$this->_validateEmail($key);
			$list[$key] = $value;
		}
		$this->{$varName} = $list;
		return $this;
	}

/**
 * Validate email address
 *
 * @param string $email Email
 * @return void
 * @throws SocketException If email address does not validate
 */
	protected function _validateEmail($email) {
		if ($this->_emailPattern === null) {
			if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
				return;
			}
		} elseif (preg_match($this->_emailPattern, $email)) {
			return;
		}
		throw new SocketException(__d('cake_dev', 'Invalid email: "%s"', $email));
	}

/**
 * Set only 1 email
 *
 * @param string $varName Property name
 * @param string|array $email String with email,
 *   Array with email as key, name as value or email as value (without name)
 * @param string $name Name
 * @param string $throwMessage Exception message
 * @return $this
 * @throws SocketException
 */
	protected function _setEmailSingle($varName, $email, $name, $throwMessage) {
		$current = $this->{$varName};
		$this->_setEmail($varName, $email, $name);
		if (count($this->{$varName}) !== 1) {
			$this->{$varName} = $current;
			throw new SocketException($throwMessage);
		}
		return $this;
	}

/**
 * Add email
 *
 * @param string $varName Property name
 * @param string|array $email String with email,
 *   Array with email as key, name as value or email as value (without name)
 * @param string $name Name
 * @return $this
 * @throws SocketException
 */
	protected function _addEmail($varName, $email, $name) {
		if (!is_array($email)) {
			$this->_validateEmail($email);
			if ($name === null) {
				$name = $email;
			}
			$this->{$varName}[$email] = $name;
			return $this;
		}
		$list = array();
		foreach ($email as $key => $value) {
			if (is_int($key)) {
				$key = $value;
			}
			$this->_validateEmail($key);
			$list[$key] = $value;
		}
		$this->{$varName} = array_merge($this->{$varName}, $list);
		return $this;
	}

/**
 * Get/Set Subject.
 *
 * @param string $subject Subject string.
 * @return string|$this
 */
	public function subject($subject = null) {
		if ($subject === null) {
			return $this->_subject;
		}
		$this->_subject = $this->_encode((string)$subject);
		return $this;
	}

/**
 * Sets headers for the message
 *
 * @param array $headers Associative array containing headers to be set.
 * @return $this
 * @throws SocketException
 */
	public function setHeaders($headers) {
		if (!is_array($headers)) {
			throw new SocketException(__d('cake_dev', '$headers should be an array.'));
		}
		$this->_headers = $headers;
		return $this;
	}

/**
 * Add header for the message
 *
 * @param array $headers Headers to set.
 * @return $this
 * @throws SocketException
 */
	public function addHeaders($headers) {
		if (!is_array($headers)) {
			throw new SocketException(__d('cake_dev', '$headers should be an array.'));
		}
		$this->_headers = array_merge($this->_headers, $headers);
		return $this;
	}

/**
 * Get list of headers
 *
 * ### Includes:
 *
 * - `from`
 * - `replyTo`
 * - `readReceipt`
 * - `returnPath`
 * - `to`
 * - `cc`
 * - `bcc`
 * - `subject`
 *
 * @param array $include List of headers.
 * @return array
 */
	public function getHeaders($include = array()) {
		if ($include == array_values($include)) {
			$include = array_fill_keys($include, true);
		}
		$defaults = array_fill_keys(
			array(
				'from', 'sender', 'replyTo', 'readReceipt', 'returnPath',
				'to', 'cc', 'bcc', 'subject'),
			false
		);
		$include += $defaults;

		$headers = array();
		$relation = array(
			'from' => 'From',
			'replyTo' => 'Reply-To',
			'readReceipt' => 'Disposition-Notification-To',
			'returnPath' => 'Return-Path'
		);
		foreach ($relation as $var => $header) {
			if ($include[$var]) {
				$var = '_' . $var;
				$headers[$header] = current($this->_formatAddress($this->{$var}));
			}
		}
		if ($include['sender']) {
			if (key($this->_sender) === key($this->_from)) {
				$headers['Sender'] = '';
			} else {
				$headers['Sender'] = current($this->_formatAddress($this->_sender));
			}
		}

		foreach (array('to', 'cc', 'bcc') as $                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                