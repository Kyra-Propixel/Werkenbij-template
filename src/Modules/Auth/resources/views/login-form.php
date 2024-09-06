<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-body text-center">
                    <h2 class="card-title"><?= phpb_trans('auth.title') ?></h2>

                    <?php if (phpb_flash('message')): ?>
                        <div class="alert alert-<?= phpb_flash('message-type') ?> mt-4">
                            <?= phpb_flash('message') ?>
                        </div>
                    <?php endif; ?>

                    <h4 class="mt-4">
                        <img src="https://dev01.propixel.nl//Logo_Transparante-achtergrond.svg" height="40px" alt="Logo"> Paneel
                    </h4>
                    <p class="mt-3">
                        De management pagina wordt beschermd door een SSO Validatie. <b>Je kunt niet handmatig inloggen.</b>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>