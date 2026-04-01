// ============================================
// validation.js — Validation JS côté client
// Couvre : connexion, articles, catégories, utilisateurs
// ============================================

(function () {
    'use strict';

    // ── Utilitaires ──
    function $(id) { return document.getElementById(id); }

    function showError(fieldId, errorId, message) {
        const field = $(fieldId);
        const span  = $(errorId);
        if (field)  field.classList.add('error');
        if (span)   span.textContent = message;
    }

    function clearError(fieldId, errorId) {
        const field = $(fieldId);
        const span  = $(errorId);
        if (field)  field.classList.remove('error');
        if (span)   span.textContent = '';
    }

    function clearAll(pairs) {
        pairs.forEach(function (p) { clearError(p[0], p[1]); });
    }

    // ── Formulaire CONNEXION ──
    const formConnexion = $('formConnexion');
    if (formConnexion) {
        formConnexion.addEventListener('submit', function (e) {
            clearAll([['login','errorLogin'],['mot_de_passe','errorMdp']]);
            let valid = true;

            const login = ($('login') || {}).value || '';
            const mdp   = ($('mot_de_passe') || {}).value || '';

            if (login.trim() === '') {
                showError('login', 'errorLogin', 'Le login est obligatoire.');
                valid = false;
            }
            if (mdp === '') {
                showError('mot_de_passe', 'errorMdp', 'Le mot de passe est obligatoire.');
                valid = false;
            }
            if (!valid) e.preventDefault();
        });

        // Validation en temps réel
        const loginInput = $('login');
        if (loginInput) {
            loginInput.addEventListener('blur', function () {
                if (this.value.trim() === '') {
                    showError('login', 'errorLogin', 'Le login est obligatoire.');
                } else {
                    clearError('login', 'errorLogin');
                }
            });
        }
    }

    // ── Formulaire ARTICLE (ajouter / modifier) ──
    const formArticle = $('formArticle');
    if (formArticle) {
        formArticle.addEventListener('submit', function (e) {
            clearAll([
                ['titre','errorTitre'],
                ['categorie_id','errorCategorie'],
                ['description_courte','errorDescription'],
                ['contenu','errorContenu'],
                ['image','errorImage']
            ]);
            let valid = true;

            const titre       = ($('titre') || {}).value || '';
            const categorieId = ($('categorie_id') || {}).value || '';
            const description = ($('description_courte') || {}).value || '';
            const contenu     = ($('contenu') || {}).value || '';
            const imageInput  = $('image');

            if (titre.trim().length < 5) {
                showError('titre', 'errorTitre', 'Le titre doit contenir au moins 5 caractères.');
                valid = false;
            } else if (titre.trim().length > 255) {
                showError('titre', 'errorTitre', 'Le titre ne doit pas dépasser 255 caractères.');
                valid = false;
            }

            if (!categorieId || categorieId === '') {
                showError('categorie_id', 'errorCategorie', 'Veuillez choisir une catégorie.');
                valid = false;
            }

            if (description.trim().length < 10) {
                showError('description_courte', 'errorDescription', 'La description doit contenir au moins 10 caractères.');
                valid = false;
            }

            if (contenu.trim().length < 20) {
                showError('contenu', 'errorContenu', 'Le contenu doit contenir au moins 20 caractères.');
                valid = false;
            }

            // Validation image (si un fichier est sélectionné)
            if (imageInput && imageInput.files.length > 0) {
                const file = imageInput.files[0];
                const allowed = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                const maxSize = 2 * 1024 * 1024; // 2 Mo

                if (!allowed.includes(file.type)) {
                    showError('image', 'errorImage', 'Format non autorisé. Utilisez JPG, PNG, GIF ou WEBP.');
                    valid = false;
                } else if (file.size > maxSize) {
                    showError('image', 'errorImage', 'L\'image ne doit pas dépasser 2 Mo.');
                    valid = false;
                }
            }

            if (!valid) {
                e.preventDefault();
                // Scroller vers la première erreur
                const firstError = formArticle.querySelector('.form-control.error');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstError.focus();
                }
            }
        });

        // Validation en temps réel pour les champs principaux
        ['titre', 'description_courte', 'contenu'].forEach(function (id) {
            const el = $(id);
            if (!el) return;
            el.addEventListener('input', function () {
                const minLengths = { titre: 5, description_courte: 10, contenu: 20 };
                const errorIds   = { titre: 'errorTitre', description_courte: 'errorDescription', contenu: 'errorContenu' };
                const labels     = { titre: 'titre', description_courte: 'description', contenu: 'contenu' };
                const min = minLengths[id];
                if (el.value.trim().length >= min) {
                    clearError(id, errorIds[id]);
                }
            });
        });
    }

    // ── Formulaire CATÉGORIE ──
    const formCategorie = $('formCategorie');
    if (formCategorie) {
        formCategorie.addEventListener('submit', function (e) {
            clearAll([['nom','errorNom']]);
            let valid = true;

            const nom = ($('nom') || {}).value || '';
            if (nom.trim().length < 2) {
                showError('nom', 'errorNom', 'Le nom doit contenir au moins 2 caractères.');
                valid = false;
            } else if (nom.trim().length > 100) {
                showError('nom', 'errorNom', 'Le nom ne doit pas dépasser 100 caractères.');
                valid = false;
            }

            if (!valid) e.preventDefault();
        });
    }

    // ── Formulaire UTILISATEUR (ajouter / modifier) ──
    const formUtilisateur = $('formUtilisateur');
    if (formUtilisateur) {
        formUtilisateur.addEventListener('submit', function (e) {
            clearAll([
                ['prenom','errorPrenom'],
                ['nom','errorNom'],
                ['login','errorLogin'],
                ['role','errorRole'],
                ['mot_de_passe','errorMdp'],
                ['mot_de_passe2','errorMdp2']
            ]);
            let valid = true;

            const prenom = ($('prenom') || {}).value || '';
            const nom    = ($('nom') || {}).value || '';
            const login  = ($('login') || {}).value || '';
            const role   = ($('role') || {}).value || '';
            const mdp    = ($('mot_de_passe') || {}).value || '';
            const mdp2   = ($('mot_de_passe2') || {}).value || '';

            if (prenom.trim().length < 2) {
                showError('prenom', 'errorPrenom', 'Le prénom doit contenir au moins 2 caractères.');
                valid = false;
            }
            if (nom.trim().length < 2) {
                showError('nom', 'errorNom', 'Le nom doit contenir au moins 2 caractères.');
                valid = false;
            }
            if (login.trim().length < 3) {
                showError('login', 'errorLogin', 'Le login doit contenir au moins 3 caractères.');
                valid = false;
            } else if (!/^[a-zA-Z0-9_.-]+$/.test(login.trim())) {
                showError('login', 'errorLogin', 'Le login ne peut contenir que des lettres, chiffres, points, tirets et underscores.');
                valid = false;
            }
            if (!role) {
                showError('role', 'errorRole', 'Veuillez choisir un rôle.');
                valid = false;
            }

            // Mot de passe : obligatoire seulement à la création (vérification via action cachée)
            const actionInput = formUtilisateur.querySelector('input[name="action"]');
            const isEdit = actionInput === null; // modifier.php n'a pas de champ action

            if (!isEdit && mdp.length < 8) {
                showError('mot_de_passe', 'errorMdp', 'Le mot de passe doit contenir au moins 8 caractères.');
                valid = false;
            } else if (isEdit && mdp !== '' && mdp.length < 8) {
                showError('mot_de_passe', 'errorMdp', 'Le nouveau mot de passe doit contenir au moins 8 caractères.');
                valid = false;
            }

            if (mdp !== '' && mdp !== mdp2) {
                showError('mot_de_passe2', 'errorMdp2', 'Les mots de passe ne correspondent pas.');
                valid = false;
            }

            if (!valid) {
                e.preventDefault();
                const firstError = formUtilisateur.querySelector('.form-control.error');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstError.focus();
                }
            }
        });

        // Vérification en temps réel de la correspondance des mots de passe
        const mdp2Field = $('mot_de_passe2');
        if (mdp2Field) {
            mdp2Field.addEventListener('input', function () {
                const mdp = ($('mot_de_passe') || {}).value || '';
                if (mdp2Field.value !== '' && mdp2Field.value !== mdp) {
                    showError('mot_de_passe2', 'errorMdp2', 'Les mots de passe ne correspondent pas.');
                } else {
                    clearError('mot_de_passe2', 'errorMdp2');
                }
            });
        }
    }

    // ── Formulaire RECHERCHE ──
    const searchForm = $('searchForm');
    if (searchForm) {
        searchForm.addEventListener('submit', function (e) {
            const input = $('searchInput');
            if (input && input.value.trim() === '') {
                e.preventDefault();
                input.focus();
            }
        });
    }

})();
