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