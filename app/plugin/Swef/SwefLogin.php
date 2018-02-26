<?php

namespace Swef;

class SwefLogin extends \Swef\Bespoke\Plugin {


/*
    PROPERTIES
*/

    public  $doLogin;


/*
    EVENT HANDLER SECTION
*/

    public function __construct ($page) {
        // Always construct the base class - PHP does not do this implicitly
        parent::__construct ($page,'\Swef\SwefLogin');
    }

    public function __destruct ( ) {
        // Always destruct the base class - PHP does not do this implicitly
        parent::__destruct ( );
    }

    public function _on_pageIdentifyBefore ( ) {
        if ($_SERVER[SWEF_STR_REQUEST_URI]==sweflogin_shortcut_in) {
            $this->doLogin = SWEF_BOOL_TRUE;
        }
        elseif ($_SERVER[SWEF_STR_REQUEST_URI]==sweflogin_shortcut_out) {
            $this->page->swef->userLogout ();
            $this->page->reload (SWEF_STR__FSLASH);
        }
        return SWEF_BOOL_TRUE;
    }

    public function _on_pageScriptBefore ( ) {
        // Already logged in
        if ($this->page->swef->userLoggedIn()) {
            $this->page->diagnosticAdd ('User already logged in');
            return SWEF_BOOL_TRUE;
        }
        // Posted login data
        if (array_key_exists(sweflogin_form_posted,$_POST)) {
            if (array_key_exists(sweflogin_form_email,$_POST)) {
                if (array_key_exists(sweflogin_form_password,$_POST)) {
                    $this->page->swef->notificationPurge ();
                    $this->page->diagnosticAdd ('Processing posted login form...');
                    $email  = $this->page->swef->userLogin (
                                  $_POST[sweflogin_form_email]
                                 ,$_POST[sweflogin_form_password]
                              );
                    if ($email) {
                        $uri        = null;
                        if ($_SERVER[SWEF_STR_REQUEST_URI]==sweflogin_shortcut_in) {
                            $uri = SWEF_STR__FSLASH;
                        }
                        elseif ($_SERVER[SWEF_STR_REQUEST_URI]==sweflogin_shortcut_out) {
                            $uri = SWEF_STR__FSLASH;
                        }
                        $this->page->diagnosticAdd ('Login successful - reloading page');
                        $this->page->reload ();
                    }
                    $this->page->diagnosticAdd ('Posted login attempt failed');
                }
            }
        }
        // Test for login intervention
        if (!$this->doLogin) {
            if ($this->page->httpE==SWEF_HTTP_STATUS_CODE_403) {
                if ($this->page->swef->context[SWEF_COL_LOGIN_ON_403]) {
                    $this->page->diagnosticAdd ('Context requires login on 403');
                    $this->doLogin = SWEF_BOOL_TRUE;
                }
                else {
                    $this->page->diagnosticAdd ('Context does not require login on 403');
                }
            }
            if ($this->page->swef->context[SWEF_COL_LOGIN_ALWAYS]) {
                $this->page->diagnosticAdd ('Context requires login ALWAYS');
                $this->doLogin = SWEF_BOOL_TRUE;
            }
            if (!$this->doLogin) {
                $this->page->diagnosticAdd ('Login is not required');
                return SWEF_BOOL_TRUE;
            }
        }
        $this->page->diagnosticAdd ('Intervening in context '.$this->page->swef->context[SWEF_COL_CONTEXT]);
        $this->page->diagnosticAdd ('Setting template = '.$this->config[SWEF_COL_TEMPLATE]);
        $this->page->template = array (
            SWEF_COL_TEMPLATE     =>$this->config[SWEF_COL_TEMPLATE]
           ,SWEF_COL_CONTENTTYPE  =>$this->config[SWEF_COL_CONTENTTYPE]
        );
        $this->page->diagnosticAdd ('Inserting login form '.sweflogin_file_login);
        require sweflogin_file_login;
        return SWEF_BOOL_FALSE;
    }



/*
    DASHBOARD SECTION
*/


    public function _dashboard ( ) {
        require_once sweflogin_file_dash;
    }

    public function _info ( ) {
        $info   = __FILE__.SWEF_STR__CRLF;
        $info  .= SWEF_COL_CONTEXT.SWEF_STR__EQUALS;
        $info  .= $this->page->swef->context[SWEF_COL_CONTEXT];
        return $info;
    }

}

?>
