CREATE TABLE Log (
    codigo           INT          NOT NULL AUTO_INCREMENT,
    tipo             VARCHAR(50)  NOT NULL,
    descricao        VARCHAR(255) NOT NULL,
    dataHora         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    codigoUtilizador INT,
    CONSTRAINT pkLogcodigo     PRIMARY KEY (codigo),
    CONSTRAINT fkLogUtilizador FOREIGN KEY (codigoUtilizador) REFERENCES Utilizador (codigo)
);
