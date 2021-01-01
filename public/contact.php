<?php

use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

// activation du système d'autoloading de Composer
require __DIR__ . '/../vendor/autoload.php';

// démarrage de la session
require "session-init.php";

// instanciation du chargeur de templates
$loader = new FilesystemLoader(__DIR__ . '/../templates');

// instanciation du moteur de template
$twig = new Environment($loader, [
    // activation du mode debug
    'debug' => true,
    // activation du mode de variables strictes
    'strict_variables' => true,
]);

// chargement de l'extension DebugExtension
$twig->addExtension(new DebugExtension());

// données du formulaire par défaut
$formData = [
    'email' => '@gmail.com',
    'subject' => 'Votre sujet',
    'message' => 'Votre message'
];

// instanciation d'un tableau d'erreur
$errors = [];

// si le bouton est presser
if ($_POST) {
    // on stock les données des inputs dans le tableau 
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

    // si aucune erreurs n'est en tableau
    if (!$errors) {
        // ajouter les valeures des inputs dans le tableau
        $formData['email'] = $_POST['email'];
        $formData['subject'] = $_POST['subject'];
        $formData['message'] = $_POST['message'];
        
        // on les affiches
        foreach ($formData as $key => $value) {
            echo ' ' . $formData[$key];
        }
        // on envoie le mail au destinataire
        // creer le transport
        $transport = (new Swift_SmtpTransport('smtp.mailtrap.io', 2525))
            ->setUsername($_SESSION["mail_username"])
            ->setPassword($_SESSION["mail_password"]);

        // creer le mail
        $mailer = new Swift_Mailer($transport);

        // creer le message
        $message = (new Swift_Message())
            ->setSubject('Nouveau message: ' . $formData['subject'])
            ->setFrom([$formData['email']])
            ->setTo(['matthieumelin62@gmail.com'])
            ->setBody($formData['message']);

        // envoyer le message
        $mailer->send($message);
    }
}

// rend les données vers la template
echo $twig->render('contact.html.twig', [
    'errors' => $errors,
    'formData' => $formData,
]);
