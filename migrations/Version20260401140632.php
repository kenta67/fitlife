<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260401140632 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE asistencia (id INT AUTO_INCREMENT NOT NULL, fecha DATE NOT NULL, hora_entrada TIME NOT NULL, hora_salida TIME DEFAULT NULL, cliente_id INT NOT NULL, INDEX IDX_D8264A8DDE734E51 (cliente_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE clase (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(100) NOT NULL, descripcion LONGTEXT DEFAULT NULL, capacidad_max INT NOT NULL, horario VARCHAR(100) DEFAULT NULL, estado TINYINT NOT NULL, instructor_id INT NOT NULL, INDEX IDX_199FACCE8C4FC193 (instructor_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE cliente (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(100) NOT NULL, apellido VARCHAR(100) NOT NULL, cedula VARCHAR(20) NOT NULL, correo VARCHAR(150) DEFAULT NULL, telefono VARCHAR(20) DEFAULT NULL, fecha_registro DATE NOT NULL, estado TINYINT NOT NULL, UNIQUE INDEX UNIQ_F41C9B257BF39BE0 (cedula), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE inscripcion_clase (id INT AUTO_INCREMENT NOT NULL, fecha DATE NOT NULL, estado TINYINT NOT NULL, cliente_id INT NOT NULL, clase_id INT NOT NULL, INDEX IDX_8EA46212DE734E51 (cliente_id), INDEX IDX_8EA462129F720353 (clase_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE membresia_cliente (id INT AUTO_INCREMENT NOT NULL, fecha_inicio DATE NOT NULL, fecha_vencimiento DATE NOT NULL, estado TINYINT NOT NULL, cliente_id INT NOT NULL, plan_id INT NOT NULL, INDEX IDX_B62AD012DE734E51 (cliente_id), INDEX IDX_B62AD012E899029B (plan_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE membresia_plan (id INT AUTO_INCREMENT NOT NULL, nombre_plan VARCHAR(100) NOT NULL, costo NUMERIC(10, 2) NOT NULL, duracion_dias INT NOT NULL, estado TINYINT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE pago (id INT AUTO_INCREMENT NOT NULL, monto NUMERIC(10, 2) NOT NULL, fecha_pago DATE NOT NULL, metodo_pago VARCHAR(50) NOT NULL, estado TINYINT NOT NULL, membresia_cliente_id INT NOT NULL, personal_id INT NOT NULL, INDEX IDX_F4DF5F3E2360BE51 (membresia_cliente_id), INDEX IDX_F4DF5F3E5D430949 (personal_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE personal (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(100) NOT NULL, usuario VARCHAR(80) NOT NULL, contrasena VARCHAR(255) NOT NULL, estado TINYINT NOT NULL, id_rol INT NOT NULL, UNIQUE INDEX UNIQ_F18A6D842265B05D (usuario), INDEX IDX_F18A6D8490F1D76D (id_rol), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE rol (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(100) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE asistencia ADD CONSTRAINT FK_D8264A8DDE734E51 FOREIGN KEY (cliente_id) REFERENCES cliente (id)');
        $this->addSql('ALTER TABLE clase ADD CONSTRAINT FK_199FACCE8C4FC193 FOREIGN KEY (instructor_id) REFERENCES personal (id)');
        $this->addSql('ALTER TABLE inscripcion_clase ADD CONSTRAINT FK_8EA46212DE734E51 FOREIGN KEY (cliente_id) REFERENCES cliente (id)');
        $this->addSql('ALTER TABLE inscripcion_clase ADD CONSTRAINT FK_8EA462129F720353 FOREIGN KEY (clase_id) REFERENCES clase (id)');
        $this->addSql('ALTER TABLE membresia_cliente ADD CONSTRAINT FK_B62AD012DE734E51 FOREIGN KEY (cliente_id) REFERENCES cliente (id)');
        $this->addSql('ALTER TABLE membresia_cliente ADD CONSTRAINT FK_B62AD012E899029B FOREIGN KEY (plan_id) REFERENCES membresia_plan (id)');
        $this->addSql('ALTER TABLE pago ADD CONSTRAINT FK_F4DF5F3E2360BE51 FOREIGN KEY (membresia_cliente_id) REFERENCES membresia_cliente (id)');
        $this->addSql('ALTER TABLE pago ADD CONSTRAINT FK_F4DF5F3E5D430949 FOREIGN KEY (personal_id) REFERENCES personal (id)');
        $this->addSql('ALTER TABLE personal ADD CONSTRAINT FK_F18A6D8490F1D76D FOREIGN KEY (id_rol) REFERENCES rol (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE asistencia DROP FOREIGN KEY FK_D8264A8DDE734E51');
        $this->addSql('ALTER TABLE clase DROP FOREIGN KEY FK_199FACCE8C4FC193');
        $this->addSql('ALTER TABLE inscripcion_clase DROP FOREIGN KEY FK_8EA46212DE734E51');
        $this->addSql('ALTER TABLE inscripcion_clase DROP FOREIGN KEY FK_8EA462129F720353');
        $this->addSql('ALTER TABLE membresia_cliente DROP FOREIGN KEY FK_B62AD012DE734E51');
        $this->addSql('ALTER TABLE membresia_cliente DROP FOREIGN KEY FK_B62AD012E899029B');
        $this->addSql('ALTER TABLE pago DROP FOREIGN KEY FK_F4DF5F3E2360BE51');
        $this->addSql('ALTER TABLE pago DROP FOREIGN KEY FK_F4DF5F3E5D430949');
        $this->addSql('ALTER TABLE personal DROP FOREIGN KEY FK_F18A6D8490F1D76D');
        $this->addSql('DROP TABLE asistencia');
        $this->addSql('DROP TABLE clase');
        $this->addSql('DROP TABLE cliente');
        $this->addSql('DROP TABLE inscripcion_clase');
        $this->addSql('DROP TABLE membresia_cliente');
        $this->addSql('DROP TABLE membresia_plan');
        $this->addSql('DROP TABLE pago');
        $this->addSql('DROP TABLE personal');
        $this->addSql('DROP TABLE rol');
    }
}
