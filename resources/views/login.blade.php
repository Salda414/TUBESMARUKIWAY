<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Marukiway</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      background-color:rgb(255, 255, 255);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .login-container {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }

    .login-box {
      background-color: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
      max-width: 800px;
      width: 100%;
      display: flex;
      flex-wrap: wrap;
      align-items: center;
    }

    .login-form {
      flex: 1;
      padding: 20px;
      min-width: 300px;
    }

    .login-form h2 {
      margin-bottom: 20px;
      text-align: center;
      font-weight: bold;
    }

    .login-form label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
    }

    .login-form input {
      width: 100%;
      padding: 10px;
      margin-bottom: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    .login-form button {
      background-color: #FFA733;
      color: white;
      border: none;
      padding: 10px;
      width: 100%;
      border-radius: 5px;
      cursor: pointer;
      font-weight: bold;
    }

    .login-form button:hover {
      background-color:#e68a00;
    }

    .illustration {
      flex: 1;
      text-align: center;
      padding: 20px;
    }

    .illustration img {
      max-width: 100%;
      height: auto;
    }

    .forgot-link {
      text-align: center;
      display: block;
      margin-top: 10px;
      font-size: 0.9rem;
    }

    .error-list {
      color: red;
      margin-bottom: 15px;
    }

    .error-list ul {
      padding-left: 20px;
      margin: 0;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <div class="login-box">
      <div class="login-form">
        <h2>LOGIN</h2>

        {{-- Validasi error --}}
        @if ($errors->any())
          <div class="error-list">
            <ul>
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form method="POST" action="{{ url('/login') }}">
          @csrf
          <label for="email">Username</label>
          <input type="email" id="email" name="email" placeholder="example@gmail.com" required>

          <label for="password">Password</label>
          <input type="password" id="password" name="password" placeholder="Password" required>

          <button type="submit">Login</button>
        </form>
      </div>
      <div class="illustration">
        <img src="{{ asset('/logo.png') }}" alt="Mirukiway">
      </div>
    </div>
  </div>
</body>
</html>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Toko Mukena</title>
  <link rel="shortcut icon" type="image/png" href="{{asset('images/logos/favicon.png')}}" />
  <link rel="stylesheet" href="{{asset('css/styles.min.css')}}" />
</head>

<body>
  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <div
      class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center">
      <div class="d-flex align-items-center justify-content-center w-100">
        <div class="row justify-content-center w-100">
          <div class="col-md-8 col-lg-6 col-xxl-3">
            <div class="card mb-0">
              <div class="card-body">
                <a href="./index.html" class="text-nowrap logo-img text-center d-block py-3 w-100">
                  <img src="{{asset('images/logos/mukena.PNG')}}" width="180" alt="">
                </a>

                <!-- Tambahan alert -->
                @if ($errors->any())
                    <div style="color: red;">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ url('/login') }}">
                  @csrf
                  <div class="mb-3">
                    <label for="email" class="form-label">Username</label>
                    <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp">
                  </div>
                  <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password">
                  </div>
                 
                  <!-- <a href="./index.html" class="btn btn-primary w-100 py-8 fs-4 mb-4 rounded-2">Sign In</a> -->
                  <button type="submit" class="btn btn-primary w-100 py-8 fs-4 mb-4 rounded-2">Login</button>
                  <div class="d-flex align-items-center justify-content-center">
                    <!-- <p class="fs-4 mb-0 fw-bold">New to Modernize?</p> -->
                    <!-- <a class="text-primary fw-bold ms-2" href="./authentication-register.html">Create an account</a> -->
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="{{asset('libs/jquery/dist/jquery.min.js')}}"></script>
  <script src="{{asset('libs/bootstrap/dist/js/bootstrap.bundle.min.js')}}"></script>
</body>

</html>

  