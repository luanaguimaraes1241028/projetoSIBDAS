CREATE TABLE MensagemContacto (
    codigo    INT          NOT NULL AUTO_INCREMENT,
    nome      VARCHAR(100) NOT NULL,
    email     VARCHAR(100) NOT NULL,
    mensagem  TEXT         NOT NULL,
    dataEnvio DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    lida      TINYINT(1)   NOT NULL DEFAULT 0,
    CONSTRAINT pkMensagemContactocodigo PRIMARY KEY (codigo)
);
