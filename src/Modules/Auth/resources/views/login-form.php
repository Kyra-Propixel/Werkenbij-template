<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <img src="https://dev01.propixel.nl//Logo_Transparante-achtergrond.svg" height="50px" alt="Bedrijfslogo" class="mb-4 fade-in">

                    <h2 class="card-title mb-4 font-weight-bold fade-in"><?= phpb_trans('auth.title') ?></h2>

                    <?php if (phpb_flash('message')): ?>
                        <div class="alert alert-<?= phpb_flash('message-type') ?> mt-4 fade-in">
                            <?= phpb_flash('message') ?>
                        </div>
                    <?php endif; ?>

                    <p class="lead mt-4 fade-in">
                        Toegang tot het beheerpaneel is alleen mogelijk via de Single Sign-On (SSO) van uw account.
                        <hr class="my-4">
                        <b>Handmatige inlogpogingen zijn niet toegestaan.</b>
                    </p>

                    <p class="text-muted mt-3 fade-in">
                        Heeft u problemen met inloggen? Neem dan contact op met de Support Afdeling.
                    </p>

                    <a href="/" class="btn btn-primary btn-lg mt-4 fade-in" style="animation-delay: 0.2s;">Terug naar Home</a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .fade-in {
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInUp 0.6s ease-in-out forwards;
    }

    .fade-in img, .fade-in h2, .fade-in p, .fade-in .btn {
        animation-delay: 0.2s;
    }

    @keyframes fadeInUp {
        0% {
            opacity: 0;
            transform: translateY(20px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Style for the button */
    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
        transition: background-color 0.3s, box-shadow 0.3s;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
    }
</style>