<?php

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2014-03-08 at 10:33:39.
 */
class AccountHandlerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var AccountHandler
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$this->object = new AccountHandler;
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown() {
		
	}

	/**
	 * @covers AccountHandler::usernameExists
	 * @covers AccountHandler::mailExists
	 * @covers AccountHandler::getMailFor
	 * @covers AccountHandler::getDataForId
	 * @covers AccountHandler::getDataFor
	 */
	public function testSimpleGetter() {
		DB::getInstance()->exec('DELETE FROM `runalyze_account` WHERE `id` = 1 OR `id` = 13');

		DB::getInstance()->insert('account',
			array('id', 'username', 'name', 'mail'),
			array(1, 'Testuser', 'Max Mustermann', 'mail@test.de')
		);

		$this->assertEquals( true, AccountHandler::usernameExists('Testuser') );
		$this->assertEquals( false, AccountHandler::usernameExists('Tester') );

		$this->assertEquals( true, AccountHandler::mailExists('mail@test.de') );
		$this->assertEquals( false, AccountHandler::mailExists('mail@test.com') );

		$this->assertEquals( 'mail@test.de', AccountHandler::getMailFor('Testuser') );
		$this->assertEquals( false, AccountHandler::getMailFor('Tester') );

		$this->assertTrue( is_array(AccountHandler::getDataForId(1)) );
		$this->assertEquals( false, AccountHandler::getDataForId(13) );

		$this->assertTrue( is_array(AccountHandler::getDataFor('Testuser')) );
		$this->assertEquals( false, AccountHandler::getDataFor('Tester') );
	}

	/**
	 * @covers AccountHandler::comparePasswords
	 * @covers AccountHandler::getAutologinHash
	 */
	public function testPasswordAndHash() {
		// Not possible without knowing the 'SALT'
	}

	/**
	 * @covers AccountHandler::tryToRegisterNewUser
	 */
	public function testTryToRegisterNewUser() {
		// TODO: Attention, process sets config-vars and imports default settings
		// Database has to be cleaned afterwards
	}

	/**
	 * @covers AccountHandler::sendPasswordLinkTo
	 */
	public function testSendPasswordLinkTo() {
		// Can't be tested
	}

	/**
	 * @covers AccountHandler::getUsernameForChangePasswordHash
	 */
	public function testGetUsernameForChangePasswordHash() {
		DB::getInstance()->insert('account',
			array('username', 'changepw_hash', 'changepw_timelimit', 'mail'),
			array('OldChanger', '8e1e915d08a163ddd4accc6d890dd557', time()-100, 'test1@mail.de')
		);
		$FirstID = DB::getInstance()->lastInsertId();

		DB::getInstance()->insert('account',
			array('username', 'changepw_hash', 'changepw_timelimit', 'mail'),
			array('NewChanger', '920676ca497a95fa7abfe6b353692613', time()+7*DAY_IN_S, 'test2@mail.de')
		);
		$SecondID = DB::getInstance()->lastInsertId();

		$this->assertEquals( false, AccountHandler::getUsernameForChangePasswordHash('') );
		$this->assertEquals( false, AccountHandler::getUsernameForChangePasswordHash('908a098ef7e6cb87de7a6') );
		$this->assertEquals( false, AccountHandler::getUsernameForChangePasswordHash('8e1e915d08a163ddd4accc6d890dd557') );
		$this->assertEquals( 'NewChanger', AccountHandler::getUsernameForChangePasswordHash('920676ca497a95fa7abfe6b353692613') );

		DB::getInstance()->exec('DELETE FROM `runalyze_account` WHERE `id`="'.$FirstID.'" OR `id`="'.$SecondID.'"');
	}

	/**
	 * @covers AccountHandler::tryToSetNewPassword
	 */
	public function testTryToSetNewPassword() {
		// Not possible (tries to forward to login.php)
	}

	/**
	 * @covers AccountHandler::tryToActivateAccount
	 */
	public function testTryToActivateAccount() {
		DB::getInstance()->exec('DELETE FROM `runalyze_account` WHERE `id` = 1');

		DB::getInstance()->insert('account',
			array('id', 'username', 'mail', 'activation_hash'),
			array(1, 'test', 'test@mail.de', '8e1e915d08a163ddd4accc6d890dd557')
		);

		$this->assertEquals( false, AccountHandler::tryToActivateAccount('908a098ef7e6cb87de7a6') );
		$this->assertEquals( '8e1e915d08a163ddd4accc6d890dd557', DB::getInstance()->query('SELECT activation_hash FROM `runalyze_account` WHERE `id`=1 LIMIT 1')->fetchColumn() );

		$this->assertEquals( true, AccountHandler::tryToActivateAccount('8e1e915d08a163ddd4accc6d890dd557') );
		$this->assertEquals( '', DB::getInstance()->query('SELECT activation_hash FROM `runalyze_account` WHERE `id`=1 LIMIT 1')->fetchColumn() );
	}

	/**
	 * @covers AccountHandler::tryToDeleteAccount
	 */
	public function testTryToDeleteAccount() {
		// FAILS because of trigger
		// PDOException: SQLSTATE[HY000]: General error: 1436 Thread stack overrun:  6024 bytes used of a 131072 byte stack, and 128000 bytes needed.  Use 'mysqld -O thread_stack=#' to specify a bigger stack.

		/*DB::getInstance()->exec('TRUNCATE TABLE `runalyze_account`');

		DB::getInstance()->insert('account',
			array('id', 'username', 'deletion_hash'),
			array(1, 'test', '8e1e915d08a163ddd4accc6d890dd557')
		);

		$_GET['delete'] = '';
		$this->assertEquals( false, AccountHandler::tryToDeleteAccount() );

		$_GET['delete'] = '908a098ef7e6cb87de7a6';
		$this->assertEquals( false, AccountHandler::tryToDeleteAccount() );

		$this->assertEquals( '8e1e915d08a163ddd4accc6d890dd557', DB::getInstance()->query('SELECT deletion_hash FROM `runalyze_account` WHERE `id`=1 LIMIT 1')->fetchColumn() );
		$this->assertEquals( true, AccountHandler::usernameExists('test') );

		$_GET['delete'] = '8e1e915d08a163ddd4accc6d890dd557';
		$this->assertEquals( true, AccountHandler::tryToDeleteAccount() );

		$this->assertEquals( false, AccountHandler::usernameExists('test') );

		DB::getInstance()->exec('TRUNCATE TABLE `runalyze_account`');*/
	}

	/**
	 * @covers AccountHandler::setAndSendDeletionKeyFor
	 */
	public function testSetAndSendDeletionKeyFor() {
		// Can't be tested
	}

}
