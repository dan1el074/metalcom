<main class="admin-login-layout">
    <section class="admin-login-brand" style="--login-background-image:url('<?= e(asset($loginImage)) ?>')"><a
            href="<?= url('/') ?>"><img src="<?= asset('assets/logo-metalcom.png') ?>" alt="Metalcom Marmentini"></a>
        <div><span class="eyebrow">Painel Metalcom</span>
            <h1>Conteúdo, contatos e usuários em um só lugar.</h1>
            <p>Área administrativa protegida para a equipe Metalcom.</p>
        </div><a class="back-site-link" href="<?= url('/') ?>">Voltar ao site</a>
    </section>
    <section class="admin-login-form-wrap">
        <form class="admin-login-form" action="<?= url('/login') ?>" method="post"><?= csrf_field() ?><span
                class="eyebrow">Área restrita</span>
            <h2>Entre na sua conta</h2><label>E-mail<input type="email" name="email" autocomplete="username"
                    required></label><label>Senha<input type="password" name="password" autocomplete="current-password"
                    required></label><button class="button button-primary button-full" type="submit">Entrar</button>
        </form>
    </section>
</main>