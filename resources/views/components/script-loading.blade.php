<script>
    document.getElementById('form').addEventListener('submit', function(e) {
        const btn = document.getElementById('submitBtn');
        const textBtn = document.querySelectorAll('.textBtn');
        const spinner = document.getElementById('spinner');
        const oldIcon = document.getElementById('iconbtn');

        textBtn.innerText = 'Processing...';
        spinner.classList.remove('hidden');
        if (oldIcon) {
            console.log(oldIcon);
            oldIcon.style.display = 'none';
        }

        btn.disabled = true;
        btn.classList.add('opacity-50', 'cursor-not-allowed');
    });
</script>
