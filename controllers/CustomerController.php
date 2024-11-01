<?php
class CustomerController extends Controller
{

    // Customer login & registration
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $formType = $_POST['form_type'];

            if ($formType === 'signup') {
                $data = [
                    'first_name' => $_POST['first_name'],
                    'last_name' => $_POST['last_name'],
                    'email' => $_POST['email'],
                    'phone' => $_POST['phone_number'],
                    'password' => $_POST['password'],
                    'confirm_password' => $_POST['confirm_password']
                ];

                // Check if the email is already in use
                if ($this->model('Customer')->isEmailTaken($data['email'])) {
                    echo json_encode(['emailTaken' => true]);
                    exit();
                }

                // If registration is successful
                if ($this->model('Customer')->register($data)) {
                    echo json_encode(['registrationSuccess' => true]);
                    exit();
                } else {
                    echo json_encode(['registrationSuccess' => false]);
                    exit();
                }
            } elseif ($formType === 'signin') {
                // Handle Sign In
                $email = $_POST['email'];
                $password = $_POST['password'];

                $user = $this->model('Customer')->login($email);
                if ($user && password_verify($password, $user['password'])) {
                    // Define the default image path
                    $defaultImagePath = 'images/user-profile.png';

                    // Check if the user has a valid image path; use default if it's empty or incomplete
                    $imagePath = (!empty($user['image_url']) && $user['image_url'] !== 'images/' && file_exists($_SERVER['DOCUMENT_ROOT'] . '/public/' . $user['image_url']))
                        ? $user['image_url']
                        : $defaultImagePath;

                    // Store user information in the session
                    $_SESSION['user'] = [
                        'id' => $user['id'],
                        'image_url' => $imagePath,
                        'name' => $user['first_name'] . ' ' . $user['last_name']
                    ];

                    echo json_encode(['loginSuccess' => true]);
                    exit();
                } else {
                    echo json_encode(['loginSuccess' => false]);
                    exit();
                }

            }
        }
        $this->view('customers/login_and_register');
    }



    // Home page for customers
    public function index()
    {
        $products = $this->model('Product')->getMainCategoriesWithProducts();
        $this->view('customers/index', ['products' => $products]);
    }

    // About Us page
    public function about()
    {
        $this->view('customers/about');
    }

    public function contact() {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_SESSION['user'])) {
                header('Location: /customers/contact?error=not_logged_in');
                exit();
            }

            $this->model('Message')->saveMessage();
            header('Location: /customers/contact?success=true');
            exit();
        }

        $this->view('customers/contact');
    }



    // Category details page
    public function categoryView($id)
    {
        $products = $this->model('Product')->getProductCategory($id);
        $this->view('customers/category', ['products' => $products]);
    }

    // Products page
    public function shop()
    {
        // Get search, category, and price range filters from GET request
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $category_id = isset($_GET['category_id']) ? $_GET['category_id'] : null; // Make sure to get category_id
        $min_price = isset($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
        $max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : 1000;

        // Pagination setup
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $itemsPerPage = 8; // Number of items to display per page

        // Calculate total items and pages
        $totalItems = $this->model('Product')->getProductCount($search, $min_price, $max_price, $category_id);
        $totalPages = ceil($totalItems / $itemsPerPage);

        // Retrieve products based on current page and filters
        $products = $this->model('Product')->getProductsByPage($search, $min_price, $max_price, $category_id, $currentPage, $itemsPerPage);

        // Pass data to the view, including pagination and filter states
        $this->view('customers/shop', [
            'products' => $products,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'search' => $search,
            'category_id' => $category_id, // Pass selected category_id
            'min_price' => $min_price,
            'max_price' => $max_price,
        ]);
    }

    public function filter()
    {
        try {
            $category_id = isset($_GET['category_id']) ? $_GET['category_id'] : null;
            $min_price = isset($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
            $max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : 1000;

            $products = $this->model('Product')->getProductsByFilter($category_id, $min_price, $max_price);

            echo json_encode(['success' => true, 'products' => $products]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }



    //rania: Product details page
    public function productDetails($id)
    {
        $product = $this->model('Product')->getProductById($id);
        $reviews = $this->model('Review')->getReviewsByProductId($id);
        $user = isset($_SESSION['user']) ? $_SESSION['user'] : null;

        $errorMessage = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($user) {
                $reviewData = [
                    'product_id' => $id,
                    'customer_id' => $user['id'],
                    'rating' => isset($_POST['rating']) ? $_POST['rating'] : null,
                    'comment' => isset($_POST['comment']) ? $_POST['comment'] : null
                ];

                if ($this->model('Review')->addReview($reviewData)) {
                    // Redirect to avoid resubmission
                    header("Location:/customers/products_details?success=true");
                    exit();
                } else {
                    $errorMessage = "Failed to submit review. Please try again.";
                }
            } else {
                $errorMessage = "Please log in to submit a review.";
            }
        }

        // Pass the error message to the view if it exists
        $this->view('customers/products_details', [
            'product' => $product,
            'reviews' => $reviews,
            'user' => $user,
            'errorMessage' => $errorMessage
        ]);
    }


    // Cart page
    public function cart()
    {
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : []; // Check if cart exists
        $this->view('customers/cart', ['cart' => $cart]); // Pass cart data to the view
    }

    // Checkout page
    public function checkout()
    {
        $this->view('customers/checkout');
    }
    public function test() {
        // Include the test view
        require_once __DIR__ . '/../views/customers/test.php';
    }

    // Profile page for customer
    public function profile()
    {
        $customer = $this->model('Customer')->getCustomerById();

        $orderitems = $this->model('OrderItem')->getOrderDetails();
        $customerId = $_SESSION['user']['id'];

        $wishlistItems = $this->model('Wishlist')->getWishlistItems($customerId);

        $this->view('customers/profile', ['customers' => $customer, 'orderitems' => $orderitems,'wishlistItems' => $wishlistItems]);

    }
    public function updateProfile()
    {
        // Fetch POST data
        $id = $_POST['id'];
        $firstName = $_POST['first_name'];
        $lastName = $_POST['last_name'];
        $email = $_POST['email'];
        $phoneNumber = $_POST['phone_number'];
        $address = $_POST['address'];
    
        // Validate and sanitize inputs here
        // Example: $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    
        // Initialize variable for image name
        $imageName = null;
    
        // Handle image upload if a file is provided
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image = $_FILES['image'];
            $imageName = time() . '_' . basename($image['name']);
            $uploadDir = 'public/uploads/'; // Specify your upload directory
            $uploadFilePath = $uploadDir . $imageName;
    
            // Move the uploaded file to the specified directory
            if (!move_uploaded_file($image['tmp_name'], $uploadFilePath)) {
                // Handle image upload error
                header("Location: profile.php?error=image_upload_failed");
                exit;
            }
        }
    
        // Use $this->model to interact with the Customer model
        $updateSuccess = $this->model('Customer')->updateCustomer($id, $firstName, $lastName, $email, $phoneNumber, $address, $imageName);
    
        if ($updateSuccess) {
            // Update the session with the new image path if an image was uploaded
            if ($imageName) {
                $_SESSION['user']['image_url'] = 'uploads/' . $imageName; // Update session variable with the new image URL
            }
            
            // Redirect or send success response
            header("Location: /customers/profile");
            exit;
        } else {
            // Handle failure
            header("Location: profile");
            exit;
        }
    }
    

    // Customer logout
    public function logout()
    {
        unset($_SESSION['id']);
        session_destroy();
        header('Location: /customers/login_and_register');
    }
}
