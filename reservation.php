<?php
/*
Plugin Name: Vehicle Reservation
Description: A plugin to calculate vehicle reservations
Version: 1.0
Author: Rafael M.
*/

session_start();

function enqueue_reservation_styles() {
    wp_enqueue_style('reservation_styles', plugin_dir_url(__FILE__) . 'reservation.css');
}

add_action('wp_enqueue_scripts', 'enqueue_reservation_styles');

function startReservation() {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $start_time = $_POST['start_time'];
        $end_time = $_POST['end_time'];

        $_SESSION['reservation'] = array(
            'start_date' => $start_date,
            'start_time' => $start_time,
            'end_date' => $end_date,
            'end_time' => $end_time,
        );

        // End output buffering
        if (ob_get_length()) {
            ob_end_clean();
        }

        // Redirect to the reservation page
        wp_redirect( home_url( 'reservas2' ) );
        exit;
    }

    return null;
}

add_action('init', 'startReservation');

function vehicle_reservation() {

    ob_start(); ?>

    <div class="container">
        <div class="process">
            <form class="form" method="post" action="">
              
                <div class="retirada">

                    <div class="local-retirada">
                        <div>
                            <label>Local de retirada</label>
                            <input type="text" name="local" placeholder="Digite o local de retirada">
                        </div>
                        
                        <div>
                            <label>Data e hora retirada</label>
                            <div class="inputs">
                                <input type="date" name="start_date">
                                <input type="time" name="start_time">
                            </div>
                        </div>

                        
                        <div>
                            <label>Data e hora devolução</label>
                            <div class="inputs">
                                <input type="date" name="end_date">
                                <input type="time" name="end_time">
                            </div>
                        </div>

                        <input class="button" type="submit" value="Continuar">
                    </div>

                </div>
            </form>
        </div>
    </div>

    <?php return ob_get_clean();
}

add_shortcode('vehicle_reservation', 'vehicle_reservation');

function display_reservation($reservation) {
    if (isset($_SESSION['reservation'])) {
        $reservation = $_SESSION['reservation'];

        echo "<section class='section'>";
        echo "<h3 class='title'>Dados da reserva</h3>";
        echo "<ul>";
        echo "<li><strong>Data de retirada: </strong>" . $reservation['start_date'] . "</li>";
        echo "<li><strong>Hora de retirada: </strong>" . $reservation['start_time'] . "</li>";
        echo "<li><strong>Data de devolução: </strong>" . $reservation['end_date'] . "</li>";
        echo "<li><strong>Hora de devolução: </strong>" . $reservation['end_time'] . "</li>";
        echo "</ul>";

        // Prepare the message
        $message = "Dados do orçamento:\n";
        $message .= "Data de retirada: " . $reservation['start_date'] . "\n";
        $message .= "Hora de retirada: " . $reservation['start_time'] . "\n";
        $message .= "Data de devolução: " . $reservation['end_date'] . "\n";
        $message .= "Hora de devolução: " . $reservation['end_time'] . "\n";

        // URL encode the message
        $message = urlencode($message);

        // Display the WhatsApp button
        echo "<a class='linka' href='https://wa.me/5573999695380?text=$message' target='_blank'>Fazer reserva</a>";
        
        echo "</section>";
    }
}

add_shortcode('display_reservation', 'display_reservation');

function complete_reservation() {
    // Define the vehicles
    $vehicles = array(
        array('image' => 'images/image1.png', 'cars' => 'Renegade, Duster, Nivus ou similar', 'category' => 'Grupo D', 'price' => 290.89),
        array('image' => 'images/image2.png', 'cars' => 'Argo, Onix, HB20 ou similar', 'category' => 'Grupo B', 'price' => 200.55),
        // Add more vehicles as needed
    );

    ob_start(); ?>

    <div class="container">
        <h2>Escolha o grupo de carros</h2>
        <form class="form" method="post" action="calculate_reservation.php">
            <?php foreach ($vehicles as $index => $vehicle): ?>
                <div class="card">
                    <?php 
                    $image_path = plugin_dir_path(__FILE__) . $vehicle['image'];
                    if (file_exists($image_path)) {
                        $image_url = plugin_dir_url(__FILE__) . $vehicle['image'];
                        echo "<img src='{$image_url}' alt='{$vehicle['cars']}'>";
                    } else {
                        echo "<p>Image not found</p>";
                    }
                    ?>
                    <h3><?php echo $vehicle['category']; ?></h3>
                    <p><?php echo $vehicle['cars'] ?></p>
                    <div class="price">
                        <span>Apartir de R$ <?php echo $vehicle['price']; ?> /dia</span><br><br>
                        <label>Escolher grupo</label>
                        <input type="radio" name="vehicle" value="<?php echo $index; ?>">
                    </div>
                </div>
            <?php endforeach; ?>
            <input type="submit" value="Continuar">
        </form>
    </div>

    <?php return ob_get_clean();
}

add_shortcode('complete_reservation', 'complete_reservation');



