<style>
    .address-page {
        min-height: 100vh;
        padding-top: 85px;
        background: #fafafa;
    }

    .address-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 40px 24px 80px;
    }

    .address-card {
        max-width: 860px;
        margin: 0 auto;
        background: #fff;
        border: 1px solid var(--color-border);
        border-radius: 14px;
        padding: 26px;
    }

    .address-title {
        font-size: 32px;
        font-weight: 400;
        letter-spacing: 0.03em;
        color: var(--color-secondary);
        margin-bottom: 8px;
    }

    .address-subtitle {
        font-size: 14px;
        color: var(--color-muted);
        margin-bottom: 22px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .field {
        margin-bottom: 2px;
    }

    .field-full {
        grid-column: span 2;
    }

    .field label {
        display: block;
        margin-bottom: 6px;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--color-muted);
    }

    .field input,
    .field select {
        width: 100%;
        height: 46px;
        border: 1px solid var(--color-border);
        border-radius: 8px;
        padding: 0 12px;
        background: #fff;
        font-size: 14px;
        color: var(--color-secondary);
    }

    .field input:focus,
    .field select:focus {
        outline: none;
        border-color: var(--color-secondary);
    }

    .error-text {
        margin-top: 6px;
        font-size: 12px;
        color: #b91c1c;
    }

    .checkbox-row {
        grid-column: span 2;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 4px;
    }

    .checkbox-row input {
        accent-color: #1a1a1a;
    }

    .checkbox-row label {
        font-size: 13px;
        color: var(--color-secondary);
        text-transform: none;
        letter-spacing: normal;
        margin-bottom: 0;
    }

    .actions {
        margin-top: 20px;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn {
        min-height: 44px;
        padding: 10px 18px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        transition: background var(--transition-fast), border-color var(--transition-fast), color var(--transition-fast);
    }

    .btn-primary {
        border: 1px solid var(--color-secondary);
        background: var(--color-secondary);
        color: #fff;
    }

    .btn-primary:hover {
        background: var(--color-hover);
        border-color: var(--color-hover);
    }

    .btn-secondary {
        border: 1px solid var(--color-border);
        background: #fff;
        color: var(--color-secondary);
    }

    .btn-secondary:hover {
        border-color: var(--color-secondary);
    }

    @media (max-width: 640px) {
        .address-page { padding-top: 70px; }
        .address-container { padding: 28px 16px 52px; }
        .address-card { padding: 20px 16px; }
        .address-title { font-size: 26px; }
        .form-grid { grid-template-columns: 1fr; }
        .field-full, .checkbox-row { grid-column: span 1; }
        .actions .btn { width: 100%; text-align: center; }
    }
</style>
