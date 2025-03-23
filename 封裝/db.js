document.addEventListener("DOMContentLoaded", function () {
    window.addUser = function () {
        const name = document.getElementById("name").value;
        const email = document.getElementById("email").value;
        const password = document.getElementById("password").value;

        fetch("add_user.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ name, email, password })
        }).then(response => response.text()).then(alert);
    };

    window.login = function () {
        const email = document.getElementById("login_email").value;
        const password = document.getElementById("login_password").value;

        fetch("login.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ email, password })
        }).then(response => response.text()).then(data => {
            alert(data);
            if (data.includes("成功")) window.location.href = "dashboard.php";
        });
    };

    window.cancelReservation = function (reservationId) {
        if (confirm("确定要取消此预约吗？")) {
            fetch(`cancel_reservation.php?id=${reservationId}`)
            .then(response => response.text()).then(data => {
                alert(data);
                window.location.reload();
            });
        }
    };
});
