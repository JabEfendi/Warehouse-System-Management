<x-sign>
    <x-slot:title>{{$title}}</x-slot:title>
    <div class="glass-container d-flex justify-content-between">
        <div class="container-image">
            <div id="carouselExampleFade" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="3000">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="{{asset('/image/bg-login.jpg')}}" class="d-block" alt="warehouse-1">
                    </div>
                    <div class="carousel-item">
                        <img src="{{asset('/image/bg-login2.jpg')}}" class="d-block" alt="warehouse-2">
                    </div>
                    <div class="carousel-item">
                        <img src="{{asset('/image/bg-login3.jpg')}}" class="d-block" alt="warehouse-3">
                    </div>
                </div>
            </div>
        </div>
        <div class="container-form" style="padding: 30px;">
            <form id="signupform">
                @csrf
                <h3 class="text-white mb-4" style="font-size: 42px;">Create an account</h3>
                <div class="row mb-3">
                    <div class="col">
                        <input type="text" class="form-control f-signin f-signin" id="first_name" placeholder="First name" aria-label="First name" required>
                    </div>
                    <div class="col">
                        <input type="text" class="form-control f-signin" id="last_name" placeholder="Last name" aria-label="Last name">
                    </div>
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control f-signin" id="username" placeholder="Username" required>
                </div>
                <div class="mb-3">
                    <input type="email" class="form-control f-signin" id="email" placeholder="Email" required>
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control f-signin" id="password" placeholder="Password" required>
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control f-signin" id="repassword" placeholder="Replay password" required>
                </div>
                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="gridCheck">
                        <label class="form-check-label" for="gridCheck" style="color: white;">I agree to the <a href="#" style="color: #8C3DD0;" required>term & condition</a></label>
                </div>
                <div class="d-grid mt-4">
                    <button type="submit" class="btn" style="background-color: #8C3DD0; color: white;">Sign Up</button>
                </div>
                <p class="text-center mt-3 text-white-50">
                    Have an account?
                    <a href="/signin" style="color: #8C3DD0;">SignIn</a>
                </p>
            </form>
        </div>
    </div>

    <script>
        document.getElementById("signupform").addEventListener("submit", async function(e) {
            e.preventDefault();

            const csrf = document.querySelector('#signupform input[name="_token"]').value;
            const firstName = document.getElementById("first_name").value.trim();
            const lastName = document.getElementById("last_name").value.trim();
            const username = document.getElementById("username").value.trim();
            const email = document.getElementById("email").value.trim();
            const password = document.getElementById("password").value;
            const repassword = document.getElementById("repassword").value;
            const status = 'pending';
            const role = 1;

            if (password !== repassword) {
                alert("Password tidak sama!");
                return;
            }

            const fullName = `${firstName} ${lastName}`.trim();

            const data = {
                name: fullName,
                username: username,
                email: email,
                password: password,
                role_id: role,
                status: status
            };

            try {
                const response = await fetch("/register", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        'Accept': 'application/json',  
                        'X-Requested-With': 'XMLHttpRequest',
                        "X-CSRF-TOKEN": csrf
                    },
                    body: JSON.stringify(data)
                });

                if (response.ok) {
                    const result = await response.json();
                    alert("Akun berhasil dibuat!");
                    console.log(result);
                    window.location.href = "/signin";
                } else {
                    const error = await response.json();
                    alert("Gagal membuat akun: " + error.message);
                }
            } catch (err) {
                console.error(err);
                alert("Terjadi kesalahan server");
            }
        });
    </script>
</x-sign>