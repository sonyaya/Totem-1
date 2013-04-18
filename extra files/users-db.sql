SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE SCHEMA IF NOT EXISTS `example` DEFAULT CHARACTER SET utf8 ;
USE `example` ;

-- -----------------------------------------------------
-- Table `example`.`_m_group`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `example`.`_m_group` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(20) NULL DEFAULT NULL ,
  `permissions` TEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 0
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `example`.`_m_user`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `example`.`_m_user` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `group_id` INT(11) NOT NULL ,
  `first_name` VARCHAR(20) NOT NULL ,
  `middle_name` VARCHAR(20) NULL DEFAULT NULL ,
  `last_name` VARCHAR(20) NOT NULL ,
  `login` VARCHAR(15) NOT NULL ,
  `password` VARCHAR(40) NOT NULL ,
  `permissions` TEXT NOT NULL ,
  `email` VARCHAR(100) NOT NULL ,
  `recovery_hash` TEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`id`, `group_id`) ,
  UNIQUE INDEX `email_UNIQUE` (`email` ASC) ,
  UNIQUE INDEX `login_UNIQUE` (`login` ASC) ,
  INDEX `user_x_group` (`group_id` ASC) ,
  CONSTRAINT `fk_user_x_group`
    FOREIGN KEY (`group_id` )
    REFERENCES `example`.`_m_group` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 0
DEFAULT CHARACTER SET = utf8;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
