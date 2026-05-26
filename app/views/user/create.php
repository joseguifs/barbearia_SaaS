<?php
$data = $data ?? [];
$errors = $errors ?? [];

if (!function_exists('e')) {
    function e($valor)
    {
        return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
    }
}

$success = isset($_GET['success']) && $_GET['success'] === '1';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>BarberTime - Cadastro</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

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
                rgba(0,0,0,0.25),
                rgba(0,0,0,0.55)
            );
        }

        .register-container{
            position:relative;
            z-index:2;

            width:100%;
            max-width:760px;

            border-radius:28px;
            overflow:hidden;

            background:rgba(18,18,18,0.42);

            backdrop-filter:blur(18px);
            -webkit-backdrop-filter:blur(18px);

            border:1px solid rgba(255,255,255,0.08);

            box-shadow:
                0 25px 70px rgba(0,0,0,0.55),
                inset 0 1px 0 rgba(255,255,255,0.04);

            opacity:0;
            transform:translateY(40px) scale(.97);

            transition:
                opacity .8s ease,
                transform .8s ease;
        }

        .register-container.show{
            opacity:1;
            transform:translateY(0) scale(1);
        }

        .register-container::before{
            content:"";
            position:absolute;

            width:340px;
            height:340px;

            top:-160px;
            right:-140px;

            border-radius:50%;

            background:
                radial-gradient(
                    circle,
                    rgba(197,157,95,.14),
                    transparent 72%
                );

            pointer-events:none;
        }

        /* ESQUERDA */
        .register-left{
            position:relative;
            z-index:1;

            width:100%;
            padding:45px 42px;
            color:#fff;

            display:flex;
            flex-direction:column;
            justify-content:center;
        }

        .logo{
            font-size:2rem;
            font-weight:bold;
            margin-bottom:8px;
            letter-spacing:1px;
        }

        .subtitle{
            color:rgba(255,255,255,.72);
            margin-bottom:34px;
            line-height:1.6;
            font-size:.96rem;
        }

        .tabs{
            display:flex;
            gap:14px;
            margin-bottom:28px;
        }

        .tab{
            text-decoration:none;

            color:rgba(255,255,255,.65);

            border:1px solid rgba(255,255,255,.10);

            background:rgba(255,255,255,.04);

            padding:12px 18px;

            border-radius:14px;

            transition:.25s ease;

            font-size:.92rem;
            font-weight:bold;
            letter-spacing:.5px;
        }

        .tab:hover{
            color:#fff;
            background:rgba(255,255,255,.08);

            transform:translateY(-2px);
        }

        .tab.active{
            background:linear-gradient(
                135deg,
                #c59d5f,
                #8b5e34
            );

            border-color:transparent;
            color:#fff;
        }

        .register-title{
            font-size:2rem;
            margin-bottom:30px;
            font-weight:700;
        }

        .form-grid{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:20px;
        }

        .full-width{
            grid-column:1 / -1;
        }

        .form-group{
            margin-bottom:4px;
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
            margin-top:12px;

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
            margin-top:18px;
            padding:14px;

            border-radius:12px;

            font-size:.92rem;
            text-align:center;
        }

        .alert.success{
            background:rgba(46,125,50,.18);

            border:1px solid rgba(76,175,80,.4);

            color:#d7ffd4;
        }

        .alert.error{
            background:rgba(183,28,28,.18);

            border:1px solid rgba(244,67,54,.35);

            color:#ffd3d3;
        }

        /* DIREITA */
        .register-right{
            display:none;
        }

        .register-right img{
            width:100%;
            height:100%;
            object-fit:cover;
        }

        .overlay-right{
            position:absolute;
            inset:0;

            background:
                linear-gradient(
                    to top,
                    rgba(0,0,0,.72),
                    rgba(0,0,0,.15)
                );

            display:flex;
            flex-direction:column;
            justify-content:flex-end;

            padding:40px;
            color:#fff;
        }

        .overlay-right h2{
            font-size:2rem;
            margin-bottom:12px;
        }

        .overlay-right p{
            color:rgba(255,255,255,.82);
            line-height:1.6;
        }

        @media(max-width:980px){

            .register-container{
                max-width:650px;
            }

            .form-grid{
                grid-template-columns:1fr;
            }
        }

        @media(max-width:600px){

            .register-left{
                padding:34px 24px;
            }

            .register-title{
                font-size:1.7rem;
            }

            .tabs{
                flex-wrap:wrap;
            }
        }

    </style>
</head>

<body>

<div class="register-container" id="registerContainer">

    <!-- ESQUERDA -->
    <div class="register-left">

        <div class="logo">BarberTime</div>

        <p class="subtitle">
            Crie sua conta e tenha acesso ao sistema de agendamentos da barbearia.
        </p>

        <div class="tabs">
            <a href="index.php?action=login" class="tab">
                LOGIN
            </a>

        </div>

        <h1 class="register-title">
            Criar Conta
        </h1>

        <form
            action="index.php?action=user_store"
            method="POST"
            id="register-form"
        >

            <div class="form-grid">

                <div class="form-group full-width">
                    <label for="nome">Nome completo</label>

                    <div class="input-wrap">
                        <input
                            type="text"
                            id="nome"
                            name="nome"
                            placeholder="Digite seu nome completo"
                            value="<?= e($data['nome'] ?? '') ?>"
                            autocomplete="name"
                        >
                    </div>

                    <?php if (!empty($errors['nome'])): ?>
                        <small class="error-text">
                            <?= e($errors['nome']) ?>
                        </small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="email">E-mail</label>

                    <div class="input-wrap">
                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="Digite seu e-mail"
                            value="<?= e($data['email'] ?? '') ?>"
                            autocomplete="email"
                        >
                    </div>

                    <?php if (!empty($errors['email'])): ?>
                        <small class="error-text">
                            <?= e($errors['email']) ?>
                        </small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="telefone">Telefone</label>

                    <div class="input-wrap">
                        <input
                            type="text"
                            id="telefone"
                            name="telefone"
                            placeholder="(63) 99999-9999"
                            value="<?= e($data['telefone'] ?? '') ?>"
                            autocomplete="tel"
                            maxlength="15"
                        >
                    </div>

                    <?php if (!empty($errors['telefone'])): ?>
                        <small class="error-text">
                            <?= e($errors['telefone']) ?>
                        </small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="senha">Senha</label>

                    <div class="input-wrap">
                        <input
                            type="password"
                            id="senha"
                            name="senha"
                            placeholder="Digite sua senha"
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
                            placeholder="Digite novamente sua senha"
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
                </div>

            </div>

            <button type="submit" class="submit-btn">
                CRIAR CONTA
            </button>

            <?php if ($success): ?>
                <div class="alert success">
                    Cadastro realizado com sucesso.
                </div>
            <?php endif; ?>

            <?php if (!empty($errors['geral'])): ?>
                <div class="alert error">
                    <?= e($errors['geral']) ?>
                </div>
            <?php endif; ?>

        </form>
    </div>
</div>

<?php if ($success): ?>
<script>
    setTimeout(() => {
        const alerta = document.querySelector('.alert.success');

        if (alerta) {
            alerta.style.display = 'none';
        }
    }, 3000);

    const url = new URL(window.location.href);

    url.searchParams.delete('success');

    window.history.replaceState(
        {},
        document.title,
        url.pathname + url.search
    );
</script>
<?php endif; ?>

<script>

    window.addEventListener('DOMContentLoaded', () => {

        const container =
            document.getElementById('registerContainer');

        setTimeout(() => {
            container.classList.add('show');
        }, 120);
    });

    const form =
        document.getElementById('register-form');

    const emailInput =
        document.getElementById('email');

    const telefoneInput =
        document.getElementById('telefone');

    const senhaInput =
        document.getElementById('senha');

    const confirmarInput =
        document.getElementById('confirmar_senha');

    const senhaHelper =
        document.getElementById('senha-helper');

    const confirmarHelper =
        document.getElementById('confirmar-helper');

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

    emailInput.addEventListener('blur', () => {

        emailInput.value =
            emailInput.value.trim().toLowerCase();
    });

    telefoneInput.addEventListener('input', () => {

        let value =
            telefoneInput.value.replace(/\D/g, '');

        if (value.length > 11) {
            value = value.slice(0, 11);
        }

        if (value.length > 10) {

            value = value.replace(
                /^(\d{2})(\d{5})(\d{4}).*/,
                '($1) $2-$3'
            );

        } else if (value.length > 6) {

            value = value.replace(
                /^(\d{2})(\d{4,5})(\d{0,4}).*/,
                '($1) $2-$3'
            );

        } else if (value.length > 2) {

            value = value.replace(
                /^(\d{2})(\d{0,5})/,
                '($1) $2'
            );

        } else if (value.length > 0) {

            value = value.replace(
                /^(\d*)/,
                '($1'
            );
        }

        telefoneInput.value = value;
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

        emailInput.value =
            emailInput.value.trim().toLowerCase();

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