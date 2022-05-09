<?php
    include 'php/data-collector.php';
    include 'php/db.php';

    // Evaluate data in $_POST variable.
    $currentQuestionIndex = 0;

    if (isset($_POST['lastQuestionIndex'])) {
        // Get data from last post.
        $lastQuestionIndex = $_POST['lastQuestionIndex'];

        if (isset($_POST['nextQuestionIndex'])) {
            // Define the index number of the next question.
            $currentQuestionIndex = $_POST['nextQuestionIndex'];
        }
    }

    // Check if $_SESSION['questions'] exists.
    if (isset($_SESSION['questions'])) {
        // echo 'questions data EXISTS in session.<br>';
        $questions = $_SESSION['questions'];
    }
    else {
        // echo 'questions data does NOT exist in session.<br>';

        // Get questions data from database using php/db.php ...
        $questions = getQuestions();

        // ... and save that data in $_SESSION. 
        $_SESSION['questions'] = $questions;
    }

    // echo '<pre>';
    // print_r($_SESSION['questions']);
    // echo '</pre>';

    // TODO: Handle transition from last question to result page.
    // TODO: Use JS validation (checkboxes, radio) with warning <p>.
    // TODO: Image from folder or from database.
    // TODO: Back button mit JS.

    include 'php/header.php';
?>

<h3>Frage <?php echo $currentQuestionIndex; ?></h3>
<p><?php echo $questions[$currentQuestionIndex]['text']; ?></p>

<form id="questionForm" <?php if ($currentQuestionIndex + 1 >= count($questions)) echo 'action="result.php" '; ?>method="post">
    <?php
        $answers = $questions[$currentQuestionIndex]['answers'];
        $isMultipleChoice = $questions[$currentQuestionIndex]['isMultipleChoice'];
        $maxPoints = 0;

        for ($a = 0; $a < count($answers); $a++) {
            echo '<div class="form-check">';
            $isCorrect = $answers[$a]['isCorrect'];

            if ($isMultipleChoice == 1) {
                // Multiple Choice (checkbox)
                echo '<input class="form-check-input" type="checkbox" name="a-' . $a . '" value="' . $isCorrect . '" id="i-' . $a . '">';
            }
            else {
                // Single Choice (radio)
                echo '<input class="form-check-input" type="radio" name="a-0" value="' . $isCorrect . '" id="i-' . $a . '">';
            }

            $maxPoints += $isCorrect; // same as: $maxPoints = $maxPoints + $isCorrect;

            echo '<label class="form-check-label" for="i-' . $a . '">';
            echo $answers[$a]['answer'];
            echo '</label>';
            echo '</div>';
        }
    ?>

    <!-- Hidden Fields -->
    <input type="hidden" id="lastQuestionIndex" name="lastQuestionIndex" value="<?php echo $currentQuestionIndex; ?>">
    <input type="hidden" id="nextQuestionIndex" name="nextQuestionIndex" value="<?php echo $currentQuestionIndex + 1; ?>">
    <input type="hidden" name="maxPoints" value="<?php echo $maxPoints; ?>">
    <!-- END Hidden Fields -->

    <p class="warning"></p>
    <input type="submit" onclick="return previousQuestion();" value="Previous Question">
    <input type="submit">
</form>

<script>
    function previousQuestion() {
        // Calculate previous question index.
        let element1 = document.getElementById("lastQuestionIndex");
        let prevousQuestionIndex = parseInt(element1.value) - 1;
        if (prevousQuestionIndex < 0) prevousQuestionIndex = 0;

        // Set the nextQuestionIndex to the previous question.
        let element2 = document.getElementById("nextQuestionIndex");
        element2.value = prevousQuestionIndex;

        /*
            Remove the action from form - otherwise submitting will
            interfere and we land on the result.php, instead of the
            previous page.
        */
        let element3 = document.getElementById("questionForm");
        element3.action = '';
    }
</script>

<?php include 'php/footer.php'; ?>