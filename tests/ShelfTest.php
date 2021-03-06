<?php declare(strict_types=1);

require_once dirname(__FILE__) . '/../config/bootstrap_tests.php';

use PHPUnit\Framework\TestCase;
use Vgsite\Shelf;

class ShelfTest extends TestCase
{
    public function testShelf()
    {
        $shelf = new Shelf();
        $shelf->addItem(new ShelfItem([]));
    }

    // public function testInvalidUser()
    // {
    //     $this->assertNull(User::getByUsername('invalid_foobar_xyz_123', $GLOBALS['pdo']));
    //     $this->assertNull(User::getById(9876543210, $GLOBALS['pdo']));
    // }

    // public function testInvalidUserException()
    // {
    //     $this->expectException(TypeError::class);
    //     User::getById('not_an_int', $GLOBALS['pdo']);
    // }

    // public function testGetUser(): User
    // {
    //     $user = User::instance($pdo, $logger)->getByUsername('test');

    //     $user = User::getByUsername('test', $GLOBALS['pdo']);
    //     $this->assertInstanceOf(User::class, $user);
    //     $this->assertEquals($user->data['email'], 'test@test.com');
    //     $this->assertEquals($user->data['user_id'], TEST_USER_ID);

    //     $user_by_id = User::getById(TEST_USER_ID, $GLOBALS['pdo']);
    //     $this->assertEquals($user, $user_by_id);

    //     return $user;
    // }

    // /**
    // @depends testGetUser
    //  */
    // public function testSaveUser(User $user)
    // {
    //     $this->assertEquals($user->data['email'], 'test@test.com');
    //     $user->data['email'] = 'foo_xyz_123@bar.com';
    //     $this->assertTrue($user->save());

    //     $user = User::getByEmail('foo_xyz_123@bar.com', $GLOBALS['pdo']);
    //     $user->data['email'] = 'test@test.com';
    //     $this->assertTrue($user->save());
    // }

    // public function testInsertUserUsernameExists()
    // {
    //     $this->expectException(Exception::class);
    //     $user_params = array(
    //         "username" => "test",
    //     );
    //     $user = new User($user_params, $GLOBALS['pdo']);
    //     $user->insert();
    // }

    // public function testInsertUserEmailExists()
    // {
    //     $this->expectException(Exception::class);
    //     $user_params = array(
    //         "email" => "test@test.com",
    //     );
    //     $user = new User($user_params, $GLOBALS['pdo']);
    //     $user->insert();
    // }

    // public function testInsertUser(): User
    // {
    //     $user_params = array(
    //         "username" => "test_foobar_123",
    //         "email" => "foo@bar.com",
    //         "password" => "password",
    //     );
    //     $user = new User($user_params, $GLOBALS['pdo']);
    //     $this->assertTrue($user->insert());
    //     $this->assertIsNumeric($user->getId());

    //     return $user;
    // }

    // /**
    // @depends testInsertUser
    //  */
    // function testInsertUserDelete(User $user)
    // {
    //     $this->assertTrue($user->delete());
    // }
}
