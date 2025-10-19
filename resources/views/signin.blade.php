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
        <div class="container-form" style="padding-right: 10%;">
            <form id="signinform" style="width: 350px;">
                @csrf
                <h3 class="text-white mb-4" style="font-size: 42px;">Sign In</h3>
                <div class="mb-3">
                    <input type="text" class="form-control f-signin" id="username" placeholder="Username" required>
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control f-signin" id="password" placeholder="Password" required>
                </div>
                <div class="d-grid mt-4">
                    <button type="submit" class="btn" style="background-color: #8C3DD0; color: white;">Sign In</button>
                </div>
                <hr style="color: white;">
                <p class="text-center mt-3 text-white-50">
                    Not have an account?
                    <a href="/signup" style="color: #8C3DD0;">Sign Up</a>
                </p>
                <p id="signin-error" class="text-danger" style="display:none;"></p>
            </form>
        </div>
    </div>
    <script>
        document.getElementById("signinform").addEventListener("submit", async function (e) {
        e.preventDefault();
        const form = e.currentTarget;
        const csrf = form.querySelector('input[name="_token"]').value;

        // const email = form.email.value.trim();
        const username = form.username.value.trim();
        const password = form.password.value.trim();

        try {
            const response = await fetch("/login", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": csrf
            },
            credentials: "same-origin", // penting agar cookie sesi tersimpan
            body: JSON.stringify({ username, password })
            });

            const result = await response.json();

            if (response.ok) {
            // redirect ke dashboard/homepage
            window.location.assign(result.redirect ?? "/");
            } else {
            document.getElementById("signin-error").style.display = "block";
            document.getElementById("signin-error").textContent = result.message || "Gagal masuk.";
            }
        } catch (err) {
            document.getElementById("signin-error").style.display = "block";
            document.getElementById("signin-error").textContent = "Terjadi kesalahan server.";
            console.error(err);
        }
        });
    </script>
</x-sign>