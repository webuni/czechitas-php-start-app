<?php

use RedBeanPHP\R;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

require_once __DIR__ . '/../vendor/autoload.php';

$templateDir = __DIR__.'/views/';

$db = R::setup()->getRedBean();

$request = Request::createFromGlobals();
$response = new Response();

$path = trim($request->getPathInfo(), '/');
$template = $path.'.html';

if ($path == '') {
    $template = 'index.html';
} elseif ($path == 'kontakt' && $request->isMethod('post')) {
    $question = $db->dispense('question');
    $question->import($request->request->all());
    $question->created = new DateTime();
    $db->store($question);

    $response = new RedirectResponse('/podekovani');
} elseif (!is_file($templateDir.$template)) {
    $template = '404.html';
    $response->setStatusCode(404);
}

$twig = new Twig_Environment(new Twig_Loader_Filesystem($templateDir));
$response->setContent($twig->render('page.html', [
    'request' => $request,
    'path' => $path,
    'template' => $template,
    'db' => $db,
]));

$response->send();
