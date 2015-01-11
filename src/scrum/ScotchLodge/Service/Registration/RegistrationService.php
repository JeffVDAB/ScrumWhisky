<?php

namespace scrum\ScotchLodge\Service\Registration;

use scrum\ScotchLodge\Entities\User;
use Doctrine\ORM\Repository;
use Valitron\Validator;
use scrum\ScotchLodge\Service\Registration\RegistrationValidation as Val;

/**
 * RegistrationService registration services
 *
 * @author jan van biervliet
 */
class RegistrationService {

  private $em;
  private $app;
  private $errors;

  function __construct($em, $app) {
    $this->app = $app;
    $this->em = $em;
    $this->errors = null;
  }

  function getEm() {
    return $this->em;
  }

  function getApp() {
    return $this->app;
  }

  public function processRegistration() {
    $em = $this->getEm();
    $app = $this->getApp();
    /* @var $user User */
    $user = new User();
    $username = $app->request->post('gebruikersnaam');
    $email = $app->request->post('email');    
    $password = $app->request->post('wachtwoord');
    $hash = password_hash($password, CRYPT_BLOWFISH);
    $first_name = $app->request->post('voornaam');
    $surname = $app->request->post('achternaam');
    
    $user->setUsername($username);
    $user->setEmail($email);    
    $user->setPassword($hash);
    $user->setFirst_name($first_name);
    $user->setSurname($surname);

    $val = new Val($app, $em);
    if ($val->validate()) {
      $em->persist($user);
      $em->flush();
      return $user;
    }
    $this->errors = $val->getErrors();
    return false;
  }
  
  public function getPostcodes() {
    $em = $this->getEm();
    $repo = $em->getRepository('scrum\ScotchLodge\Entities\Postcode');
    $postcodes = $repo->findAll();
    return $postcodes;
  }
  
  public function getErrors() {
    return $this->errors;
  }
}