<?php

namespace scrum\ScotchLodge\Service\Profile;

use Doctrine\ORM\EntityManager;
use scrum\ScotchLodge\Entities\User;
use scrum\ScotchLodge\Service\Validation\ProfileValidation as Val;

/**
 * ProfileService
 *
 * @author jan van biervliet
 */
class ProfileService {

  private $em;
  private $app;
  private $errors;

  public function __construct($em, $app) {
    $this->em = $em;
    $this->app = $app;
  }

  public function retrieveUserByUsername($username) {
    $repo = $this->em->getRepository('scrum\ScotchLodge\Entities\User');
    $user = $repo->findBy(array('username' => $username));
    return count($user) > 0 ? $user[0] : null;
  }

  public function confirmPassword($username, $password) {
    /* var $user User */
    $user = $this->retrieveUserByUsername($username);
    $this->user = $user;

    if (isset($user) && $user != null) {
      $hash = $user->getPassword();

      if (password_verify($password, $hash)) {
        return $user;
      } else {
        return null;
      }
    } else {
      return false;
    }
  }
  
   
  public function dataIsValid() {
    $val = new Val($this->app, $this->em);
    $validated = $val->validate();
    $this->errors = $val->getErrors();
    return $validated;
  }
  
  public function updateUser(User $user) {
    $app = $this->app;
    $em = $this->em;
    $repo = $em->getRepository('scrum\ScotchLodge\Entities\User');    
    
    $password = $app->request->post('wachtwoord');    
    if ( isset($password) && trim($password) != '') {
      $hash = password_hash($password, CRYPT_BLOWFISH);
      $user->setPassword($hash);
    }
    
    $first_name = $app->request->post('voornaam');
    if ($user->getFirstName() !=  $first_name) {
      $user->setFirstName($first_name);
    }
    
    $surname = $app->request->post('achternaam');
    if ($user->getSurname() != $surname) {
      $user->setSurname($surname);
    }
    
    $address = $app->request->post('adres');
    if ($user->getAddress() != $address) {
      $user->setAddress($address);
    }
    
    $em->persist($user);
    $em->flush();
    
  }
  
  public function getErrors() {
    return $this->errors;
  }
  
  public function getUser() {
    return $this->user;
  }
  
  

}