CREATE DATABASE treinosdesportivos;

CREATE TABLE utilizador (
    id INT NOT NULL AUTO_INCREMENT,
    username VARCHAR(30) NOT NULL,
    pass VARCHAR(40) NOT NULL,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    morada VARCHAR(100),
    PRIMARY KEY (id)
);

CREATE TABLE administrador(
    id INT NOT NULL AUTO_INCREMENT,
    utilizador_id INT NOT NULL,
    PRIMARY KEY (id),
    CONSTRAINT FK_UTILIZADOR_ADMIN
        FOREIGN KEY (utilizador_id)
            REFERENCES utilizador(id)
);

CREATE TABLE atleta(
    id INT NOT NULL AUTO_INCREMENT,
    utilizador_id INT NOT NULL,
    PRIMARY KEY (id),
    CONSTRAINT FK_UTILIZADOR_ATLETA
        FOREIGN KEY (utilizador_id)
            REFERENCES utilizador(id)
);

CREATE TABLE treinador(
    id INT NOT NULL AUTO_INCREMENT,
    utilizador_id INT NOT NULL,
    PRIMARY KEY (id),
    CONSTRAINT FK_UTILIZADOR_TREINADOR
        FOREIGN KEY (utilizador_id)
            REFERENCES utilizador(id)
);

CREATE TABLE atletaTreinador(
    atleta_id INT NOT NULL,
    treinador_id INT NOT NULL,
    PRIMARY KEY(atleta_id, treinador_id),
    CONSTRAINT FK_ATLETA_ATLETATREINADOR
        FOREIGN KEY (atleta_id)
            REFERENCES atleta(id),
    CONSTRAINT FK_TREINADOR_ATLETATREINADOR
        FOREIGN KEY (treinador_id)
            REFERENCES treinador(id)  
);

CREATE TABLE plano(
    id INT NOT NULL AUTO_INCREMENT,
    treinador_id INT NOT NULL,
    nome VARCHAR(100) NOT NULL,
    descricao VARCHAR(255),
    PRIMARY KEY (id),
    CONSTRAINT FK_TREINADOR_PLANO
        FOREIGN KEY (treinador_id)
            REFERENCES treinador(id)
);

CREATE TABLE planoAtleta(
    plano_id INT NOT NULL,
    atleta_id INT NOT NULL,
    data_inicial DATE, 
    data_final DATE,
    PRIMARY KEY (plano_id, atleta_id),
    CONSTRAINT FK_PLANO_PLANOATLETA
        FOREIGN KEY (plano_id)
            REFERENCES plano(id),
    CONSTRAINT FK_ATLETA_PLANOATLETA
        FOREIGN KEY (atleta_id)
            REFERENCES atleta(id)
);

CREATE TABLE bloco(
    id INT NOT NULL AUTO_INCREMENT,
    plano_id INT NOT NULL,
    ordem INT NOT NULL,
    PRIMARY KEY (id),
    CONSTRAINT FK_PLANO_BLOCO
        FOREIGN KEY (plano_id)
            REFERENCES plano(id)
);

CREATE TABLE tipoExercicio(
    id INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    descricao VARCHAR(255),
    PRIMARY KEY (id)
);

CREATE TABLE exercicio (
    id INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    descricao VARCHAR(255),
    tipoExercicio_id INT,
    PRIMARY KEY (id),
    CONSTRAINT FK_TIPOEXERCICIO_EXERCICIO
        FOREIGN KEY (tipoExercicio_id)
            REFERENCES tipoExercicio(id)
);

CREATE TABLE blocoExercicio (
    bloco_id INT NOT NULL,
    exercicio_id INT NOT NULL,
    series INT,
    repeticoes INT,
    carga INT,
    tempo_distancia VARCHAR(100),
    realizado boolean,
    PRIMARY KEY (bloco_id, exercicio_id),
    CONSTRAINT FK_BLOCO_BLOCOEXERCICIO
        FOREIGN KEY (bloco_id)
            REFERENCES bloco(id),
    CONSTRAINT FK_EXERCICIO_BLOCOEXERCICIO
        FOREIGN KEY (exercicio_id)
            REFERENCES exercicio(id)
);