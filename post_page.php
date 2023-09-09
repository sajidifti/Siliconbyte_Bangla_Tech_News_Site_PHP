<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page or display a message
    $error = "পাতাটি দেখতে আগে সাইনইন করুন।";
    header("Location: signin_page.php?error=" . urlencode($error));
    exit();
}

if (isset($_SESSION['role'])) {

    if ($_SESSION['role'] != "writer") {
        // Redirect to the login page or display a message
        $error = "পাতাটি শুধুমাত্র লেখকদের জন্য বরাদ্দ।";
        header("Location: index.php?error=" . urlencode($error));
        exit();
    }

}
// Include your database connection file
include('db-connection.php');

// Fetch user information based on the user_id from the session
$user_id = $_SESSION['user_id'];
$sql = "SELECT fullname, username, email, profile_photo FROM Users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // User found, fetch and display their information
    $row = $result->fetch_assoc();
    $fullname = $row['fullname'];
    $username = $row['username'];
    $email = $row['email'];
    $profile_photo = $row['profile_photo'];
} else {
    // User not found, handle the error
    echo "User not found.";
}

// Close the database connection
$stmt->close();
$conn->close();
?>
<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--fonts-->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=PT+Sans:wght@400;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS  link-->
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/media.css">
    <title>সিলিকনবাইট</title>
</head>

<body>
    <!-- header start-->
    <header>
        <div class="container">
            <!-- nav start-->
            <nav class="navbar navbar-expand-lg navbar-light">
                <div class="container-fluid">
                    <!--logo-->
                    <a class="navbar-brand" href="index.php">
                        <img src="images/stock/logo.png" class="img-fluid logo" alt="সিলিকনবাইট">
                    </a>
                    <!-- hambarger btn-->
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav_c"
                        aria-controls="nav_c" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <!--nav items-->

                    <div class="collapse navbar-collapse menu" id="nav_c">
                        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                            <li class="nav-item">
                                <a class="nav-link" href="index.php">হোম</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="articles.php">সব খবর</a>
                            </li>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <?php if ($_SESSION['role'] == "writer"): ?>
                                    <li class="nav-item">
                                        <a class="nav-link active-link" href="post_page.php">লিখুন</a>
                                    </li>
                                <?php endif; ?>
                                <?php if ($_SESSION['role'] == "admin"): ?>
                                    <li class="nav-item">
                                        <a class="nav-link" href="admin.php">ব্যাবস্থাপনা</a>
                                    </li>
                                <?php endif; ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="profile.php">প্রোফাইল</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="signout.php">সাইনআউট</a>
                                </li>
                            <?php else: ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="signup_page.php">সাইনআপ</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="signin_page.php">সাইনইন</a>
                                </li>
                            <?php endif; ?>

                        </ul>

                    </div>
                </div>
            </nav>

            <!-- nav end-->
        </div>
    </header>
    <!-- header end-->
    <!-- Error message display -->
    <section class="message">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col">
                    <?php
                    if (!empty($_GET['error'])) {
                        $errorMessage = urldecode($_GET['error']);
                        echo '<div class="alert alert-danger text-center" role="alert">' . $errorMessage . '</div>';
                    }
                    if (!empty($_GET['success'])) {
                        $successMessage = urldecode($_GET['success']);
                        echo '<div class="alert alert-success text-center" role="alert">' . $successMessage . '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </section>
    <!-- Error message display end -->

    <section class="post">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9">
                    <div class="c_title text-center">
                        <h1 class="c_h1 yellow form-title">প্রতিবেদন লিখুন</h1>
                        <p class="c_p ash">শিরোনাম, সারাংশ, বর্ণনা, ও ছবি দিয়ে খবর প্রকাশ করুন</p>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="c_form">
                        <form action="post.php" method="POST" enctype="multipart/form-data">
                            <div class="row g-3 justify-content-center">
                                <!-- Article Title -->
                                <div class="col-lg-9 col-md-9">
                                    <input type="text" class="form-control" id="title" name="title" required
                                        placeholder="শিরোনাম">
                                </div>

                                <!-- Article Summary -->
                                <div class="col-lg-9 col-md-9">
                                    <textarea class="form-control" id="summary" name="summary" maxlength="50" required
                                        placeholder="সারাংশ (৫০ অক্ষরের মধ্যে)"></textarea>
                                </div>

                                <!-- Article Content -->
                                <div class="col-lg-9 col-md-9">
                                    <textarea class="form-control" id="content" name="content" rows="5" required
                                        placeholder="বর্ণনা"></textarea>
                                </div>

                                <!-- Article Photo -->
                                <div class="col-lg-9 col-md-9">
                                    <input type="file" class="form-control" id="photo" name="photo" accept="image/*"
                                        required placeholder="ছবি">
                                </div>

                                <!-- Article Category -->
                                <div class="col-lg-9 col-md-9">
                                    <label for="category" class="form-label">বিভাগ</label>
                                    <select class="form-select" id="category" name="category" required>
                                        <option value="smartphone">Smartphone</option>
                                        <option value="pc">PC</option>
                                        <option value="software">Software</option>
                                        <option value="tutorial">Tutorial</option>
                                        <option value="programing">Programing</option>
                                        <option value="gaming">Gaming</option>
                                    </select>
                                </div>

                                <!-- Tags -->
                                <!-- Tags -->
                                <div class="col-lg-9 col-md-9">
                                    <div class="d-flex justify-content-center text-center">

                                        <?php
                                        // Include the database connection file
                                        include 'db-connection.php';

                                        // Retrieve tags from the Tags table
                                        $sql = "SELECT * FROM Tags";
                                        $result = $conn->query($sql);

                                        // Create five tag select fields
                                        for ($i = 1; $i <= 5; $i++) {
                                            echo '<div class="me-3 d-inline-block">'; // Add the d-inline-block class here
                                            echo '<label for="tags' . $i . '" class="form-label">ট্যাগ ' . $i . '</label>';
                                            echo '<select class="form-select" id="tags' . $i . '" name="tag' . $i . '">'; // Note the name="tag1", "tag2", etc.
                                        
                                            // Populate the select list with options
                                            while ($row = $result->fetch_assoc()) {
                                                echo '<option value="' . $row['tag_id'] . '">' . $row['tag_name'] . '</option>';
                                            }

                                            echo '</select>';
                                            echo '</div>';

                                            // Reset the result pointer to the beginning for the next select list
                                            $result->data_seek(0);
                                        }

                                        // Close the database connection
                                        $conn->close();
                                        ?>

                                    </div>
                                </div>


                                <!-- End tag -->





                            </div>
                            <!-- Submit Button -->
                            <div class="col-lg-2 col-md-2 container-fluid">
                                <button type="submit" class="btn c_button" style="margin-top: 5rem;">প্রকাশ
                                    করুন</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php

    // Include the footer file
    include_once('footer.php');

    ?>

    <!-- Scroll to The Top -->
    <div class="scroll-to-top" id="scrollButton" onclick="scrollToTop()">
        ^
    </div>


    <!--  Bootstrap JS link -->
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="https://kit.fontawesome.com/c204687a77.js" crossorigin="anonymous"></script>
    <script src="js/script.js"></script>
</body>

</html>