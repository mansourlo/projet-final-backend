// main.js — ActuDynamic v2

document.addEventListener('DOMContentLoaded', function () {

    // ── Menu mobile ──
    const toggle  = document.getElementById('navToggle');
    const navList = document.getElementById('navList');
    if (toggle && navList) {
        toggle.addEventListener('click', function () {
            const open = navList.classList.toggle('open');
            toggle.textContent = open ? '✕' : '☰';
        });
        document.addEventListener('click', function (e) {
            if (toggle && navList && !toggle.contains(e.target) && !navList.contains(e.target)) {
                navList.classList.remove('open');
                toggle.textContent = '☰';
            }
        });
    }

    // ── Auto-fermeture des alertes flash ──
    document.querySelectorAll('.alert').forEach(function (el) {
        setTimeout(function () {
            el.style.transition = 'opacity .5s, transform .5s';
            el.style.opacity = '0';
            el.style.transform = 'translateY(-8px)';
            setTimeout(function () { el.remove(); }, 500);
        }, 4500);
    });

    // ── Prévisualisation image pour file-drop ──
    const imageInput = document.getElementById('image');
    if (imageInput) {
        imageInput.addEventListener('change', function () {
            const file = this.files[0];
            const hint = document.getElementById('fileDropHint');
            const wrap = document.getElementById('imagePreviewWrap');
            const img  = document.getElementById('imagePreview');
            const name = document.getElementById('imagePreviewName');

            if (!file) return;

            if (file.size > 2 * 1024 * 1024) {
                if (hint) hint.textContent = '⚠️ Fichier trop lourd (max 2 Mo)';
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                if (img)  { img.src = e.target.result; }
                if (name) { name.textContent = file.name + ' (' + (file.size / 1024).toFixed(0) + ' Ko)'; }
                if (wrap) { wrap.style.display = 'block'; }
                if (hint) { hint.textContent = '✓ Image sélectionnée'; }
            };
            reader.readAsDataURL(file);
        });

        // Drag & drop
        const dropZone = document.getElementById('fileDrop');
        if (dropZone) {
            ['dragenter','dragover'].forEach(function (ev) {
                dropZone.addEventListener(ev, function (e) {
                    e.preventDefault();
                    dropZone.style.borderColor = 'var(--primary)';
                    dropZone.style.background = 'var(--primary-light)';
                });
            });
            ['dragleave','drop'].forEach(function (ev) {
                dropZone.addEventListener(ev, function (e) {
                    e.preventDefault();
                    dropZone.style.borderColor = '';
                    dropZone.style.background = '';
                });
            });
        }
    }

});

function togglePassword(fieldId, btn) {
    const field = document.getElementById(fieldId);
    if (!field) return;
    field.type = field.type === 'password' ? 'text' : 'password';
    btn.textContent = field.type === 'password' ? '👁' : '🙈';
}
