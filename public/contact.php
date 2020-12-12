<?php

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

// activation du système d'autoloading de Composer
require __DIR__.'/../vendor/autoload.php';

// instanciation du chargeur de templates
$loader = new FilesystemLoader(__DIR__.'/../templates');

// instanciation du moteur de template
$twig = new Environment($loader);

// form data
$formData = [
    'email' => '@gmail.com',
    'subject' => '',
    'message' => ''
];
$errors = [];

if ($POST) {
    foreach ($formData as $key => $value) {
        if (isset($_POST[$key])) {
            $formData[$key] = $_POST[$key];
        }
    }

    $length = [3, 190, 1000];

    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    if (empty($email)) {
        $errors['email'] = 'Merci de renseigner ce champ.';
    } elseif (strlen($email) > $length[1]) {
        $errors['email'] = "Merci de renseigner un email dont la longueur ne dépasse pas les {$length[1]}";
    } elseif (preg_match('/^[a-zA-Z]+$/', $email === 0)) {
        $errors['email'] = 'Merci de renseigner un login composé uniquement de lettres de l\'alphabet sans accent';
    } elseif (filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
        $errors['email'] = 'Merci de renseigner un email valide';
    }

    if (!$errors) {
        $url = '/';
        header("Location: {$url}", true, 302);
        exit();
    }
    
    if (empty($subject)) {
        $errors['subject'] = 'Merci de renseigner un sujet';
    } elseif (strlen($subject) < $length[0] || strlen($subject) > $length[1]) {
        $errors['subject'] = "Merci de renseigner un email dont la longueur est comprise entre {$length[0]} et {$length[1]}";
    }

    if (empty($message)) {
        $errors['message'] = 'Merci de renseigner un message';
    } elseif (strlen($message) < $length[0] || strlen($subject) > $length[2]) {
        $errors['message'] = "Merci de renseigner un email dont la longueur est comprise entre {$length[0]} et {$length[1]}";
    }
}

// display template render
echo $twig->render('contact.html.twig', [
    'errors' => $errors,
    'formData' => $formData,
]);