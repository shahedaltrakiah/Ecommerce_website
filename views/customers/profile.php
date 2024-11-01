<?php require "views/partials/header.php"; ?>

<link href="/public/css/user_profile_style.css" rel="stylesheet">

<div class="main-container">
    <!-- Profile Section -->
    <div class="profile-card">
        <button class="profile-edit-btn" data-bs-toggle="modal" data-bs-target="#editProfileModal">Edit Profile</button>
        <div class="profile-info">
        <img src="<?php echo isset($_SESSION['user']['image_url']) ? '/public/' . $_SESSION['user']['image_url'] : '/public/images/user-profile.png'; ?>" alt="Profile Image">

            <div>
                <h3><?php echo htmlspecialchars($customers->first_name . ' ' . $customers->last_name); ?></h3>
            </div>
        </div>
        <div class="profile-details mt-4">
            <div class="row">
                <div class="col-md-6 mt-3">
                    <label>First Name</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($customers->first_name); ?>" readonly>
                </div>
                <div class="col-md-6 mt-3">
                    <label>Last Name</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($customers->last_name); ?>" readonly>
                </div>
                <div class="col-md-6 mt-3">
                    <label>Email</label>
                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($customers->email); ?>" readonly>
                </div>
                <div class="col-md-6 mt-3">
                    <label>Phone Number</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($customers->phone_number); ?>" readonly>
                </div>
                <div class="col-md-12 mt-3">
                    <label>Address</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($customers->address); ?>" readonly>
                </div>
            </div>
        </div>
    </div>
<!-- Edit Profile -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="/customers/profile/update" method="POST" enctype="multipart/form-data" id="editProfileForm"onsubmit="return validateForm()">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($customers->id); ?>"> <!-- Hidden ID field -->

                        <div class="col-md-6">
                            <label>First Name</label>
                            <input type="text" class="form-control" name="first_name" value="<?php echo htmlspecialchars($customers->first_name); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label>Last Name</label>
                            <input type="text" class="form-control" name="last_name" value="<?php echo htmlspecialchars($customers->last_name); ?>" required>
                        </div>
                        <div class="col-md-12 mt-3">
                            <label>Email</label>
                            <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($customers->email); ?>" required>
                        </div>
                        <div class="col-md-12 mt-3">
                            <label>Phone Number</label>
                            <input type="text" class="form-control" name="phone_number" value="<?php echo htmlspecialchars($customers->phone_number); ?>" pattern="^[\d\+\-\.\(\)\/\s]*$" required>
                        </div>
                        <div class="col-md-12 mt-3">
                            <label>Address</label>
                            <input type="text" class="form-control" name="address" value="<?php echo htmlspecialchars($customers->address); ?>" required>
                        </div>
                        <div class="col-md-12 mt-3">
                            <label>Profile Image</label>
                            <input type="file" class="form-control" name="image" accept="image/*"> <!-- Image upload field -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="saveChangesBtn">Save Changes</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>


    <!-- Order History Section -->
    <div class="order-card">
        <h4>Order History</h4>
        <table class="table order-table">
            <thead>
            <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Status</th>
                <th>Total</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($orderitems as $order): ?>
                <tr>
                    <td>#<?= htmlspecialchars($order->order_id) ?></td>
                    <td><?= htmlspecialchars(date('M d, Y', strtotime($order->order_date))) ?></td>
                    <td><span class="badge bg-<?= $order->status == 'completed' ? 'success' : 'warning' ?>"><?= htmlspecialchars(ucfirst($order->status)) ?></span></td>
                    <td><sup> JD </sup><?= number_format($order->total_amount, 2) ?></td>
                    <td>
                        <button class="view-details-btn" data-bs-toggle="modal"
                                data-bs-target="#orderDetailsModal" data-order-id="<?= $order->order_id ?>">View Details</button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal for Viewing Order Details -->
    <div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderDetailsModalLabel">Order Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="order-details">
                        <h5>Order ID: <span class="text-primary">#<?= htmlspecialchars($order->order_id) ?></span></h5>
                        <p><strong>Date:</strong> <span class="text-muted"><?= htmlspecialchars(date('M d, Y', strtotime($order->order_date))) ?></span></p>
                        <p><strong>Status:</strong> <span class="badge bg-<?= $order->status == 'completed' ? 'success' : 'warning' ?>"><?= htmlspecialchars(ucfirst($order->status)) ?></span></p>
                        <p><strong>Total:</strong> <span class="text-danger"><sup> JD </sup><?= number_format($order->total_amount, 2) ?></span></p>
                        <p><strong>Shipping Address:</strong> <span class="text-muted">Amman,Jordan</span>
                        </p>
                        <p><strong>Items Ordered:</strong></p>
                        <div class="order-items">
                            <?php foreach ($orderitems as $order): ?>
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?= htmlspecialchars($order->product_name) ?>
                                    <span class="badge bg-secondary"><sup> JD </sup><?= number_format($order->product_price, 2) ?></span>
                                </li>
                            </ul>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Wishlist Section -->
<!-- Wishlist Section -->
<div class="order-card">
    <h4>Your Wishlist</h4>
    <div class="wishlist-slider">
        <?php if (!empty($wishlistItems)): ?>
            <?php foreach ($wishlistItems as $item): ?>
                <div class="wishlist-item" style="width: 100%; max-width: 250px; height: 350px;">
                    <img class="wishlist-image" src="<?= htmlspecialchars('/public/' . $item['image_url']) ?>" alt="<?= htmlspecialchars($item['product_name']) ?>">
                    <div class="wishlist-details text-center">
                        <h6 class="text-dark"><?= ucwords(str_replace(['-', '_'], ' ', htmlspecialchars($item['product_name']))); ?></h6>
                        <p class="text-muted"><sup> JD </sup><?= htmlspecialchars($item['price']) ?></p>
                        <form class="remove-wishlist-form" action="/customers/profile/remove" method="POST">
                            <input type="hidden" name="product_id" value="<?= htmlspecialchars($item['product_id']) ?>">
                            <button type="submit" class="btn btn-danger btn-sm remove-btn">Remove</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center">
                <p class="text-muted">No items in your wishlist.</p>
            </div>
        <?php endif; ?>
    </div>
</div>


<!-- Slick CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>

<!-- jQuery (only load once) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Slick Carousel JavaScript -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

<!-- SweetAlert Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Popper and Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

<script>
    function validateForm() {
    const form = document.getElementById('editProfileForm');
    const firstName = form.elements["first_name"].value.trim();
    const lastName = form.elements["last_name"].value.trim();
    const email = form.elements["email"].value.trim();
    const phoneNumber = form.elements["phone_number"].value.trim();
    const address = form.elements["address"].value.trim();
    const image = form.elements["image"].value;

    // Regular expression patterns
    const namePattern = /^[A-Za-z\s]{2,30}$/; // letters only, 2-30 characters
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // basic email pattern
    const phonePattern = /^[\d\+\-\.\(\)\/\s]*$/; // allows numbers, parentheses, +, -, ., /

    // Validate First Name
    if (!namePattern.test(firstName)) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid First Name',
            text: 'Please enter a valid first name (letters only, 2-30 characters).',
        });
        return false;
    }

    // Validate Last Name
    if (!namePattern.test(lastName)) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Last Name',
            text: 'Please enter a valid last name (letters only, 2-30 characters).',
        });
        return false;
    }

    // Validate Email
    if (!emailPattern.test(email)) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Email',
            text: 'Please enter a valid email address.',
        });
        return false;
    }

    // Validate Phone Number
    if (!phonePattern.test(phoneNumber) || phoneNumber.length < 7) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Phone Number',
            text: 'Please enter a valid phone number.',
        });
        return false;
    }

    // Validate Address (check if it's not empty)
    if (address === "") {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Address',
            text: 'Please enter your address.',
        });
        return false;
    }

    // Optional: Validate Profile Image if required (checking if an image file is selected)
    if (image && !image.match(/\.(jpg|jpeg|png|gif)$/)) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Image',
            text: 'Please upload a valid image file (jpg, jpeg, png, gif).',
        });
        return false;
    }

    // Confirm Save Changes
    Swal.fire({
        title: 'Are you sure?',
        text: "You want to save the changes to your profile!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, save changes!'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit(); // Submit the form if confirmed
        }
    });

    return false; // Prevent default form submission until confirmation
}


    function removeItem(itemName, form) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You want to remove " + itemName + " from your wishlist!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, remove it!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Submit the form to remove the item
            form.submit(); // Submit the form after confirmation
            Swal.fire({
                title: 'Removed!',
                text: itemName + ' has been removed from your wishlist.',
                icon: 'success',
                confirmButtonColor: '#3B5D50'
            });
        }
    });
}

// Example: Attach this function to your button click event
$('.remove-btn').on('click', function(e) {
    e.preventDefault(); // Prevent the default form submission
    const form = $(this).closest('form'); // Get the closest form
    const itemName = form.find('input[name="product_name"]').val(); // You may want to adjust this to get the actual product name instead
    removeItem(itemName, form); // Call the removeItem function
});
$(document).ready(function(){
    if ($('.wishlist-slider .wishlist-item').length >= 4) {
        $('.wishlist-slider').slick({
            slidesToShow: 4,
            slidesToScroll: 1,
            infinite: false,
            dots: true,
            responsive: [
                {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 2
                    }
                },
                {
                    breakpoint: 576,
                    settings: {
                        slidesToShow: 1
                    }
                }
            ]
        });
    }
});

</script>
