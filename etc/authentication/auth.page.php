<?php

require_once(dirname(__FILE__) . '/../../core/main.inc.php');

/**
 * This class handles the functions which are needed to process an authentication
 * for the given page.
 */
abstract class AuthPage extends Page {
  /**
   * Contains the authenticatable module
   * @var IAuthenticatable The authenticatable module
   */
  protected $AuthenticationModule;

  /**
   * Contains the bailout page. This page is set as current page in the kernel
   * if the authenticatable module is null or the returns false.
   * @var Page The bailout page.
   */
  protected $BailoutPage;

  /**
   * Initializes the host.
   */
  public function __construct($host) {
      parent::__construct($host);
      $this->BailoutPage = null;
      $this->AuthenticationModule = null;
  }

  /**
   * Sets the auth module for this class. $module must be of the type IAuthenticatable.
   * @param IAuthenticatable The authenticatable module
   * @return void
   */
  public function SetAuthModule($module) {
      if(!($module instanceof IAuthenticatable))
          throw new WrongTypeException("The authenticatable module must be of the type IAuthenticatable!");

      $this->AuthenticationModule = $module;
  }

  /**
   * Initializes the auth module and authenticate the current user. If the
   * data isn't valid it invokes the bailout page.
   */
  public function Initialize() {
    $page = $this->GetBailoutPage();

    if($this->AuthenticationModule == null) {
        $this->Host->InvokePage($page);
    }
    else {
        $this->AuthenticationModule->SetCredentials($this->GetAuthModuleCredentials());

        if($this->AuthenticationModule->Authenticate()) {
            /* nothing do here - the user is authenticated for this page */
        }
        else {
            $this->Host->InvokePage($page);
        }
    }
  }

  /**
   * Returns the bailout page. If BailoutPage is null it returns the standard page.
   * @return Page
   */
  private function GetBailoutPage() {
      if($this->BailoutPage == null) {
          return new Standard();
      }

      return $this->BailoutPage;
  }

  /**
   * This method is for initializing the auth module. Just grab the data you need
   * for it and return it.
   * @return array(mixed)
   */
  public abstract function GetAuthModuleCredentials();

  /**
   * Gets the current set authenticatable module.
   * @return IAuthenticatable
   */
  public function GetAuthModule() { return $this->AuthenticationModule; }
}

?>
