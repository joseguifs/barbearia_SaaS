<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Agendamento</title>
    <link rel="stylesheet" href="/barbearia_SaaS/app/css/scheduling.css">
</head>
<body>
    <header class="topbar">
        <div class="logo">BARBERTIME</div>

        <div class="user-area">
            <span class="user-name">AGENDAMENTO</span>
            <div class="avatar"></div>
        </div>
    </header>

    <main class="page">

        <?php if (!empty($success)): ?>
            <div class="success-message">
                Agendamento cadastrado com sucesso.
            </div>
        <?php endif; ?>

        <form action="index.php?action=scheduling_store" method="POST" class="schedule-form">

            <!-- CLIENTE -->
            <section class="card">
                <h2>Selecione o cliente</h2>

                <div class="field-group">
                    <label for="cliente_id">Cliente</label>
                    <select id="cliente_id" name="cliente_id" required>
                        <option value="">Selecione um cliente</option>
                        <?php if (!empty($clientes)): ?>
                            <?php foreach ($clientes as $cliente): ?>
                                <option value="<?= $cliente['id_cliente'] ?>">
                                    <?= htmlspecialchars($cliente['nome']) ?>
                                    <?php if (!empty($cliente['email'])): ?>
                                        - <?= htmlspecialchars($cliente['email']) ?>
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </section>

            <!-- BARBEIRO -->
            <section class="card">
                <h2>Selecione o barbeiro</h2>

                <div class="barbeiros-lista">
                    <?php if (!empty($barbeiros)): ?>
                        <?php foreach ($barbeiros as $barbeiro): ?>
                            <label class="barbeiro-item">
                                <input
                                    type="radio"
                                    name="barbeiro_id"
                                    value="<?= $barbeiro['id_barbeiro'] ?>"
                                    required
                                >

                                <span class="barbeiro-avatar"></span>

                                <span class="barbeiro-info">
                                    <strong><?= htmlspecialchars($barbeiro['nome']) ?></strong>
                                    <small><?= htmlspecialchars($barbeiro['especialidades'] ?? 'Sem serviços cadastrados') ?></small>
                                </span>

                                <span class="checkmark">✔</span>
                            </label>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="empty-message">Nenhum barbeiro disponível.</p>
                    <?php endif; ?>
                </div>
            </section>

            <!-- SERVIÇOS -->
            <section class="card">
                <h2>Selecione os serviços</h2>
                <p class="section-text">Você pode marcar um ou mais serviços.</p>

                <div class="servicos-grid">
                    <?php if (!empty($servicos)): ?>
                        <?php foreach ($servicos as $servico): ?>
                            <label class="servico-item">
                                <input
                                    type="checkbox"
                                    name="servicos[]"
                                    value="<?= $servico['id_servico'] ?>"
                                >

                                <span class="servico-content">
                                    <strong><?= htmlspecialchars($servico['nome']) ?></strong>
                                    <small>
                                        R$ <?= number_format((float)$servico['preco'], 2, ',', '.') ?>
                                        <?php if (!empty($servico['duracao'])): ?>
                                            · <?= (int)$servico['duracao'] ?> min
                                        <?php endif; ?>
                                    </small>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="empty-message">Nenhum serviço disponível.</p>
                    <?php endif; ?>
                </div>
            </section>

            <!-- DATA E HORÁRIO -->
            <section class="card">
                <h2>Data e horário</h2>

                <div class="datetime-grid">
                    <div class="field-group">
                        <label for="data_agendamento">Data</label>
                        <input
                            type="date"
                            id="data_agendamento"
                            name="data_agendamento"
                            required
                        >
                    </div>

                    <div class="field-group">
                        <label for="hora_agendamento">Horário</label>
                        <select id="hora_agendamento" name="hora_agendamento" required>
                            <option value="">Selecione um horário</option>
                            <option value="09:00">09:00</option>
                            <option value="09:30">09:30</option>
                            <option value="10:00">10:00</option>
                            <option value="10:30">10:30</option>
                            <option value="11:00">11:00</option>
                            <option value="14:00">14:00</option>
                            <option value="14:30">14:30</option>
                            <option value="15:00">15:00</option>
                            <option value="15:30">15:30</option>
                            <option value="16:00">16:00</option>
                        </select>
                    </div>
                </div>
            </section>

            <!-- DESCRIÇÃO -->
            <section class="card">
                <h2>Descrição</h2>

                <div class="field-group">
                    <label for="descricao">Observações</label>
                    <textarea
                        id="descricao"
                        name="descricao"
                        rows="5"
                        placeholder="Ex.: preferência de corte, observações sobre barba, encaixe, etc."
                    ></textarea>
                </div>
            </section>

            <div class="form-actions">
                <button type="submit" class="btn-confirmar">Confirmar agendamento</button>
            </div>
        </form>
    </main>
</body>
</html>