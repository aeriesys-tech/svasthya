<template>
    <!-- standard header -->
    <header class="adminuiux-header">
        <!-- Fixed navbar -->
        <nav class="navbar">
            <div class="container-fluid">
                <!-- logo -->
                <!-- <a class="navbar-brand" href="ecommerce-dashboard.html">
                    <img data-bs-img="light" src="/images/logo.png" alt="">
                    <img data-bs-img="dark" src="/images/logo.png" alt="">
                    <div class="d-block ps-2">
                        <h6 class="fs-6 mb-0">Union Catering Company</h6>
                        <p class="company-tagline">Exquisite Event Services</p>
                    </div>
                </a> -->
                <div class=" ms-auto ">
                    <br>
                    <img data-bs-img="light" src="/images/logo.png" alt="" width="100px">
                    <img data-bs-img="dark" src="/images/logo.png" alt="" width="100px">
                </div>
                <!-- right icons button -->
                <div class="ms-auto">
                </div>
            </div>
        </nav>
    </header>
    <div class="adminuiux-wrap z-index-0 position-relative">
        <main class="adminuiux-content">
            <!--Page body-->
            <div class="container-fluid">
                <div class="row gx-3 align-items-center justify-content-center py-3 mt-auto z-index-1 height-dynamic" style="--h-dynamic: calc(100vh - 120px)">
                    <div class="col login-box maxwidth-400">
                        <div class="mb-4">
                            <h3 class="text-theme fw-normal mb-0">Sorry! you have to be here,</h3>
                            <h1 class="fw-bold text-theme-accent mb-2">Reset Password</h1>
                            <p class="text-secondary">Please provide your registered email address. We will send a one-time password (OTP) to your email to verify your identity and proceed with the next steps.</p>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="emailadd" placeholder="Enter email address" autofocus>
                            <label for="emailadd">Email Address</label>
                        </div>
                        <a href="ecommerce-change-password.html" class="btn btn-lg btn-theme w-100 mb-4">Reset Now</a>
                        <!-- <button class="btn btn-lg btn-theme w-100 mb-4">Login</button> -->
                        <br>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>