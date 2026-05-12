<?php
$errors = $errors ?? [];
$email = $_GET['email'] ?? '';

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
        content="width=device-width, initial-scale=1.0"
    >

    <style>

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:Arial, Helvetica, sans-serif;
        }

        body{
            min-height:100vh;

            display:flex;
            align-items:center;
            justify-content:center;

            overflow:hidden;

            background:#0f0f0f;

            position:relative;

            padding:20px;
        }

        body::before{
            content:"";

            position:absolute;
            inset:0;

            background:
                linear-gradient(
                    rgba(0,0,0,.25),
                    rgba(0,0,0,.45)
                ),
                url('assets/images/backgroundLogin.jpeg')
                center center/cover no-repeat;

            transform:scale(1.08);

            filter:
                blur(4px)
                brightness(0.42);
        }

        body::after{
            content:"";

            position:absolute;
            inset:0;

            background:linear-gradient(
                rgba(0,0,0,.25),
                rgba(0,0,0,.55)
            );
        }

        .reset-wrapper{
            position:relative;
            z-index:2;

            width:100%;
            max-width:520px;
        }

        .reset-card{
            position:relative;

            background:rgba(18,18,18,0.42);

            backdrop-filter:blur(18px);
            -webkit-backdrop-filter:blur(18px);

            border:1px solid rgba(255,255,255,.08);

            border-radius:28px;

            padding:42px 34px;

            box-shadow:
                0 25px 70px rgba(0,0,0,.55),
                inset 0 1px 0 rgba(255,255,255,.04);

            overflow:hidden;

            opacity:0;
            transform:translateY(40px) scale(.97);

            transition:
                opacity .8s ease,
                transform .8s ease;
        }

        .reset-card.show{
            opacity:1;
            transform:translateY(0) scale(1);
        }

        .reset-card::before{
            content:"";

            position:absolute;

            width:320px;
            height:320px;

            top:-140px;
            right:-120px;

            border-radius:50%;

            background:
                radial-gradient(
                    circle,
                    rgba(197,157,95,.14),
                    transparent 72%
                );

            pointer-events:none;
        }

        .reset-title{
            position:relative;
            z-index:1;

            font-size:2rem;
            margin-bottom:14px;
            font-weight:700;

            color:#fff;
        }

        .reset-description{
            position:relative;
            z-index:1;

            color:rgba(255,255,255,.72);

            font-size:.96rem;
            line-height:1.6;

            margin-bottom:34px;
        }

        .auth-form{
            position:relative;
            z-index:1;
        }

        .form-group{
            margin-bottom:24px;
        }

        .form-group label{
            display:block;

            margin-bottom:10px;

            color:rgba(255,255,255,.92);

            font-size:.95rem;
        }

        .input-wrap{
            position:relative;

            width:100%;

            border-radius:14px;

            border:1px solid rgba(255,255,255,.10);

            background:rgba(255,255,255,.035);

            transition:
                border-color .25s ease,
                box-shadow .25s ease,
                background .25s ease;
        }

        .input-wrap:hover{
            background:rgba(255,255,255,.06);
        }

        .input-wrap.is-focused{
            border-color:#c59d5f;

            box-shadow:
                0 0 0 4px rgba(197,157,95,.10),
                0 10px 25px rgba(0,0,0,.15);

            background:rgba(255,255,255,.06);
        }

        .input-wrap input{
            width:100%;
            height:56px;

            padding:0 52px 0 16px;

            background:transparent;

            border:none;
            outline:none;

            color:#fff;

            font-size:1rem;

            border-radius:14px;
        }

        .input-wrap input::placeholder{
            color:rgba(255,255,255,.42);
        }

        .toggle-password{
            position:absolute;

            top:50%;
            right:14px;

            transform:translateY(-50%);

            background:none;
            border:none;

            color:rgba(255,255,255,.72);

            cursor:pointer;

            font-size:.88rem;

            transition:.25s;
        }

        .toggle-password:hover{
            color:#e7c48d;
        }

        .helper-text,
        .error-text,
        .match-text{
            display:block;

            margin-top:8px;

            font-size:.84rem;
        }

        .helper-text{
            color:rgba(255,255,255,.58);
        }

        .error-text{
            color:#ff9c9c;
        }

        .match-text.ok{
            color:#9fe2a5;
        }

        .match-text.error{
            color:#ff9c9c;
        }

        .submit-btn{
            width:100%;

            margin-top:8px;

            border:none;
            border-radius:14px;

            background:linear-gradient(
                135deg,
                #c59d5f,
                #8b5e34
            );

            color:#fff;

            height:58px;

            font-size:1rem;
            font-weight:bold;
            letter-spacing:.5px;

            cursor:pointer;

            transition:
                transform .2s ease,
                opacity .2s ease,
                box-shadow .2s ease;
        }

        .submit-btn:hover{
            transform:translateY(-2px);

            opacity:.96;

            box-shadow:
                0 10px 25px rgba(197,157,95,.22);
        }

        .alert{
            margin-bottom:22px;

            padding:14px;

            border-radius:12px;

            font-size:.92rem;

            text-align:center;
        }

        .alert.error{
            background:rgba(183,28,28,.18);

            border:1px solid rgba(244,67,54,.35);

            color:#ffd3d3;
        }

        .actions{
            display:flex;

            gap:12px;

            margin-top:18px;
        }

        .action-link{
            flex:1;

            text-align:center;
            text-decoration:none;

            padding:14px;

            border-radius:14px;

            font-size:.92rem;
            font-weight:bold;

            transition:.25s ease;
        }

        .action-link.login{
            background:rgba(255,255,255,.05);

            color:#fff;

            border:1px solid rgba(255,255,255,.10);
        }

        .action-link.login:hover{
            background:rgba(255,255,255,.08);

            transform:translateY(-2px);
        }

        @media(max-width:600px){

            .reset-card{
                padding:34px 24px;
            }

            .reset-title{
                font-size:1.7rem;
            }

            .actions{
                flex-direction:column;
            }
        }

    </style>
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

                <input
                    type="hidden"
                    name="email"
                    value="<?= e($email) ?>"
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