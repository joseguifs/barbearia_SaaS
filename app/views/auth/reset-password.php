<?php
$errors = $errors ?? [];

if (!function_exists('e')) {
    function e($valor)
    {
        return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">

    <title>BarberTime - Redefinir Senha</title>

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="/barbearia_SaaS/app/css/reset-password.css">


</head>

<body>

    <div class="reset-wrapper">

        <section class="reset-card" id="reset-card">

            <h1 class="reset-title">
                Redefinir Senha
            </h1>

            <p class="reset-description">
                Crie uma nova senha segura para continuar acessando sua conta.
            </p>

            <?php if (!empty($errors['geral'])): ?>
                <div class="alert error">
                    <?= e($errors['geral']) ?>
                </div>
            <?php endif; ?>

            <form
                action="index.php?action=reset_password"
                method="POST"
                class="auth-form"
                id="reset-form"
            >
                <div class="form-group">

                    <label for="senha">
                        Nova senha
                    </label>

                    <div class="input-wrap">

                        <input
                            type="password"
                            id="senha"
                            name="senha"
                            placeholder="Digite a nova senha"
                            autocomplete="new-password"
                        >

                        <button
                            type="button"
                            class="toggle-password"
                            data-target="senha"
                        >
                            Mostrar
                        </button>

                    </div>

                    <small
                        id="senha-helper"
                        class="helper-text"
                    >
                        Use pelo menos 6 caracteres.
                    </small>

                    <?php if (!empty($errors['senha'])): ?>
                        <small class="error-text">
                            <?= e($errors['senha']) ?>
                        </small>
                    <?php endif; ?>

                </div>

                <div class="form-group">

                    <label for="confirmar_senha">
                        Confirmar senha
                    </label>

                    <div class="input-wrap">

                        <input
                            type="password"
                            id="confirmar_senha"
                            name="confirmar_senha"
                            placeholder="Digite novamente a senha"
                            autocomplete="new-password"
                        >

                        <button
                            type="button"
                            class="toggle-password"
                            data-target="confirmar_senha"
                        >
                            Mostrar
                        </button>

                    </div>

                    <small
                        id="confirmar-helper"
                        class="helper-text"
                    >
                        Repita exatamente a senha informada.
                    </small>

                    <?php if (!empty($errors['confirmar_senha'])): ?>
                        <small class="error-text">
                            <?= e($errors['confirmar_senha']) ?>
                        </small>
                    <?php endif; ?>

                </div>

                <button
                    type="submit"
                    class="submit-btn"
                >
                    REDEFINIR SENHA
                </button>

                <div class="actions">

                    <a
                        href="index.php?action=login"
                        class="action-link login"
                    >
                        VOLTAR AO LOGIN
                    </a>

                </div>

            </form>

        </section>

    </div>

    <script>

        window.addEventListener('DOMContentLoaded', () => {

            const card =
                document.getElementById('reset-card');

            setTimeout(() => {
                card.classList.add('show');
            }, 120);
        });

        const senhaInput =
            document.getElementById('senha');

        const confirmarInput =
            document.getElementById('confirmar_senha');

        const senhaHelper =
            document.getElementById('senha-helper');

        const confirmarHelper =
            document.getElementById('confirmar-helper');

        const form =
            document.getElementById('reset-form');

        document
            .querySelectorAll('.input-wrap input')
            .forEach((input) => {

                const wrap =
                    input.closest('.input-wrap');

                input.addEventListener('focus', () => {
                    wrap.classList.add('is-focused');
                });

                input.addEventListener('blur', () => {
                    wrap.classList.remove('is-focused');
                });
            });

        senhaInput.addEventListener('input', () => {

            const senha = senhaInput.value;

            if (senha.length === 0) {

                senhaHelper.textContent =
                    'Use pelo menos 6 caracteres.';

                senhaHelper.className =
                    'helper-text';

                return;
            }

            if (senha.length < 6) {

                senhaHelper.textContent =
                    'Senha fraca: use pelo menos 6 caracteres.';

                senhaHelper.className =
                    'match-text error';

            } else if (senha.length < 8) {

                senhaHelper.textContent =
                    'Senha razoável.';

                senhaHelper.className =
                    'match-text ok';

            } else {

                senhaHelper.textContent =
                    'Senha forte.';

                senhaHelper.className =
                    'match-text ok';
            }

            validarConfirmacaoSenha();
        });

        confirmarInput.addEventListener(
            'input',
            validarConfirmacaoSenha
        );

        function validarConfirmacaoSenha(){

            const senha =
                senhaInput.value;

            const confirmar =
                confirmarInput.value;

            if(!confirmar){

                confirmarHelper.textContent =
                    'Repita exatamente a senha informada.';

                confirmarHelper.className =
                    'helper-text';

                return;
            }

            if(senha === confirmar){

                confirmarHelper.textContent =
                    'As senhas coincidem.';

                confirmarHelper.className =
                    'match-text ok';

            } else {

                confirmarHelper.textContent =
                    'As senhas não coincidem.';

                confirmarHelper.className =
                    'match-text error';
            }
        }

        document
            .querySelectorAll('.toggle-password')
            .forEach((button) => {

                button.addEventListener('click', () => {

                    const target =
                        document.getElementById(
                            button.dataset.target
                        );

                    const isPassword =
                        target.type === 'password';

                    target.type =
                        isPassword ? 'text' : 'password';

                    button.textContent =
                        isPassword
                            ? 'Ocultar'
                            : 'Mostrar';
                });
            });

        form.addEventListener('submit', (event) => {

            if(
                senhaInput.value &&
                confirmarInput.value &&
                senhaInput.value !== confirmarInput.value
            ){

                event.preventDefault();

                confirmarHelper.textContent =
                    'As senhas não coincidem.';

                confirmarHelper.className =
                    'match-text error';

                confirmarInput.focus();
            }
        });

    </script>

</body>
</html>