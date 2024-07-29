<?php
// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // reCAPTCHA settings
    $recaptcha_secret = "6LdnaBoqAAAAAIJd2deen-MbKkxsbwldSHK5sAIg";
    $recaptcha_threshold = 0.5; // Adjust this threshold as needed

    // Form data
    $name = strip_tags(trim($_POST["name"]));
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $subject = strip_tags(trim($_POST["subject"]));
    $message = strip_tags(trim($_POST["message"]));
    $recaptcha_response = $_POST['g-recaptcha-response'];

    // Verify reCAPTCHA
    $verify_response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$recaptcha_secret}&response={$recaptcha_response}");
    $response_data = json_decode($verify_response);

    if (!$response_data->success) {
        http_response_code(400);
        echo json_encode(["error" => "reCAPTCHA verification failed. Please try again."]);
        exit;
    }

    if ($response_data->score < $recaptcha_threshold) {
        http_response_code(400);
        echo json_encode(["error" => "reCAPTCHA score too low. Please try again or contact us directly."]);
        exit;
    }

    // Validate other form data
    if (empty($name) OR empty($message) OR !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(["error" => "There was a problem with your submission. Please complete all fields and try again."]);
        exit;
    }

    // Prepare email
    $recipient = "joshbatties@jpcgroup.com";
    $email_content = "Name: $name\n";
    $email_content .= "Email: $email\n\n";
    $email_content .= "Subject: $subject\n\n";
    $email_content .= "Message:\n$message\n";
    $email_headers = "From: $name <$email>";

    // Send email
    if (mail($recipient, "New contact form submission", $email_content, $email_headers)) {
        http_response_code(200);
        echo json_encode(["message" => "Thank you! Your message has been sent."]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Oops! Something went wrong and we couldn't send your message."]);
    }

} else {
    http_response_code(403);
    echo json_encode(["error" => "There was a problem with your submission, please try again."]);
}