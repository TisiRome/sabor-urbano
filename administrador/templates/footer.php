    <script>
        let inactividad=900; 

        let temporizador;
        function resetTimer() {
            clearTimeout(temporizador);
            temporizador=setTimeout(() => {
                window.location.href="cerrar.php";
            }, inactividad * 1000);
        }

        // Detectar actividad del usuario
        window.onload=resetTimer;
        document.onmousemove=resetTimer;
        document.onkeypress=resetTimer;
        document.onclick=resetTimer;
        document.onscroll=resetTimer;
    </script>
    <script src="https://kit.fontawesome.com/b85253dc75.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.min.js" integrity="sha384-VQqxDN0EQCkWoxt/0vsQvZswzTHUVOImccYmSyhJTp7kGtPed0Qcx8rK9h9YEgx+" crossorigin="anonymous"></script>
</body>
</html>