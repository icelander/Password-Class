<?php

/**
* Password Class
*/
class Password
{
   private $password_salt;
   private $password;
   
   function __construct($password, $salt = null)
   {
      if (is_null($password) || empty($password))
      {
         throw new Exception("Password can't be empty");
      }
      
      $this->password = $password;
      
      if (!is_null($salt))
      {
         $this->salt = $salt;
      }
      else
      {
         $this->salt = 'ilHEJdwCWYPw48Cpxi5xErQQXOeO3C3L5gq43zpCfWgj5uLu9V0b9sbOWcOuJXs';
      }
      
   }
   
   function getPassword()
   {
      return $this->password;
   }
   
   public function getSalt()
   {
      return $this->salt;
   }
   
   public function getSaltedPassword()
   {
      $salted_password = array();
      
      if (strlen($this->password) > strlen($this->salt)) 
      {
         $s = $this->salt;
         $p = $this->password;
         
         $this->salt = $p;
         $this->password = $s;
      }
      
      $password_length = strlen($this->password);
      $salt_length = strlen($this->salt);
      $step = ($salt_length % $password_length);
      $total_length = $password_length + $salt_length;
      $password_char = 0;
      $salt_char = 0;
      
      while (count($salted_password) < $total_length) {
         if (count($salted_password) > $password_length && ($salt_char % $step) == 1 && $password_char < $password_length)
         {
            $salted_password[] = $this->password[$password_char];
            $password_char++;
         }
         
         $salted_password[] = $this->salt[$salt_char];
         $salt_char++;
         if ($salt_char > $salt_length)
         {
            $salt_char = 0;
         }
      }
      
      return implode($salted_password);
   }
}

class PasswordTest extends PHPUnit_Framework_TestCase
{
   public function testConstructor()
   {
      $test_password = 'test password';
      $test_salt = 'test salt';
      
      $password = new Password($test_password, $test_salt);
      
      $this->assertEquals($password->getPassword(), $test_password);
      $this->assertEquals($password->getSalt(), $test_salt);
   }
   
   // If the passed salt is null it should use a pre-defined salt
   // If the passed salt is not null then it should use that salt
   public function testNullSalt()
   {
      $test_password = 'test password';
      
      $password = new Password($test_password);
      $this->assertNotNull($password->getSalt());
      $this->assertEquals('ilHEJdwCWYPw48Cpxi5xErQQXOeO3C3L5gq43zpCfWgj5uLu9V0b9sbOWcOuJXs', $password->getSalt());
   }
   
   // __construct should return false on null password, regardless of if salt is set
   /**
    * @expectedException Exception
    */
   public function testNullPassword()
   {
      new Password(null);
      new Password(null, 'foobar');
   }
   
   public function testSaltingPassword()
   {
      $test_password = 'abc123'; // Length 6
      $test_salt = 'qwertyuiopasdfghjklzxcvbnm'; // Length: 26
      // so we go half the length of the password into the salt and start interspersing the characters of the hash every n characters, where n is the hash length & password length
      $expected_salted_password = 'qwertyuaiobpacsd1fg2hj3klzxcvbnm';
      
      $password = new Password($test_password, $test_salt);
      $salted_password = $password->getSaltedPassword();
      $this->assertEquals($expected_salted_password, $salted_password);
   }
}
