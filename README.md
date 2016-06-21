# Behoma

A plugin for light weight Web API Framework Hoimi.
this is supporting for building a Html Response.

## features

- multi language
- Form Helper
- Onetime token(for CSRF) 
- PRG

## How To Use

1. add ["t_ishida/Behoimi" : "dev-master"] to require field of in composer.json
2. Building Hoimi Application.
3. Building ActionClasses for rendering web forms or web pages is extends BaseGetActionBase
4. Building ActionClasses for receiving posts is extends BasePOSTActionBase
5. Building HTML Templates 

### DirectoryList(Recommended)

- public
  - index.php
- app
  - actions => ActionsClasses
    - Index.php(example)
    - Form.php(example)
    - Save.php(example)
  - classes => AnyClasses
  - lang => Language Files
    - ja.php(example)
    - en.php(example)
    - Internationalization.php
  - resources => ConfigFiles
    - database.php(example)
    - config.php
  - responses => ResponseClasses
    - IndexView.php(example)
  - templates => Template
    - IndexView.php(example)
  - bootstrap.php 
  - routes.php 
- tests(UnitTests)

### example - public/index.php

```
<?php
require __DIR__  . '/../app/bootstrap.php';
$zaolik = \Zaolik\DIContainer::getInstance();
$router = $zaolik->getFlyWeight('router');
$config = $zaolik->getFlyWeight('config');
$request = new \Hoimi\Request($_SERVER, $_REQUEST);
$response = null;
try {
    list($action, $method) = $router->run($request);
    $action->setConfig($config);
    $action->setRequest($request);
    if ($action->useSessionVariables()) {
        $session = new \Hoimi\Session($request, $config->get('session', array()));
        $action->setSession($session);
        $session->start();
        $response = $action->$method();
        $session->flush();
    } else {
        $response = $action->$method();
    }
} catch (\Hoimi\BaseException $e) {
    var_dump($e);
    $response = $e->buildResponse();
} catch (\Exception $e) {
    var_dump($e);
    $response = new \Hoimi\Response\Error($e);
}
foreach ($response->getHeaders() as $header) {
    header($header);
}
echo $response->getContent();
```

### example - bootstrap.php

```
<?php
error_reporting(E_ALL);
ini_set('display.errors', 1);
define('APP_ROOT', __DIR__);
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/resources/config.php';
require __DIR__ . '/zaolik.php';
require __DIR__ . '/lang/Internationalization.php';
set_error_handler(function($errno, $errstr, $errfile, $errline){
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});
```

### example - GETAction

```
<?php
namespace Sample\actions;


use Behoma\Web\BaseGetAction;
use Hoimi\Request;
use Sample\responses\IndexView;

class Index extends BaseGetAction
{
    public function getActionFormName()
    {
        return 'Sample\classes\forms\SampleForm';
    }

    public function doGet(Request $request)
    {
        return new IndexView(\Zaolik\DIContainer::getInstance()->getFlyWeight('International'));
    }
}
```

### example - POSTAction

```
<?php
namespace Sample\actions;


use Behoma\Web\BasePostAction;
use Behoma\Web\Redirect;

class Save extends BasePostAction
{

    public function getActionFormName()
    {
        return 'Sample\classes\forms\SampleForm';
    }

    public function formUrl()
    {
        return '/';
    }

    public function getValidatorDefinitions()
    {
        return array (
            'text' => array('required' => true, 'dataType' => 'integer'),
        );
    }

    public function doPost()
    {
        return new Redirect('/?saved=true');
    }
}
```

### example - ResponseCLass

```
<?php
namespace Sample\responses;
use Sample\classes\BaseHtmlResponse;

class IndexView extends BaseHtmlResponse
{
    private $actionForm = null;

    /**
     * @return null
     */
    public function getActionForm()
    {
        return $this->actionForm;
    }

    /**
     * @param null $actionForm
     */
    public function setActionForm($actionForm)
    {
        $this->actionForm = $actionForm;
    } 
    
    /// these methods are same codes in a application.
    /// recommend to pull up to abstract class.
    public function appRoot()
    {
       return APP_ROOT;
    }

    public function nameSpaceRoot()
    {
       return 'Sample';
    }

    public function responseDirectoryName()
    {
       return APP_ROOT . '/responses';
    }

    public function templateDirectoryName()
    {
       return APP_ROOT . '/templates';
    }
}
```

### example - templates

```
<?php
// $response is Response class.
// $actionForm is ActionForm
?>
<html>
  <body>
    <p><?php $response->assignWord('word1')?></p>
    <p><?php $response->assignWord('word2')?></p>
    <p><?php $response->assignWord('word3')?></p>

    <?php $actionForm->formStart('form', '/save', 'POST')?>
    <?php $actionForm->formText('text')?>
    <?php if (!$actionForm->isValid('text')):?>
      <p><?php $response->assignMessage($actionForm->getErrors('text'))?>
    <?php endif; ?>
    <?php $actionForm->formSubmit('hogehoge')?>
    <?php $actionForm->formEnd()?>
  </body>
</html>
```

### example - routes.php(see also Hoimi)

```
<?php
// routing settings
return new \Hoimi\Router(array(
    '/' => '\Sample\actions\Index',
    '/index' => '\Sample\actions\Index',
    '/save' => '\Sample\actions\Save',
));
```

### example - config.php(see also Hoimi)

```
<?php
// read files in same directory.
return new \Hoimi\Config(__FILE__);
```

### example - internationalization.php

```
// read files in same directory.
return (new \Behoma\Core\Internationalization(__FILE__))
    ->setDefault('ja')
    ->setAccept(array('ja', 'en'))
    ->setLanguage($_SERVER['HTTP_ACCEPT_LANGUAGE']);
```


## License

This library is available under the MIT license. See the LICENSE file for more info.

