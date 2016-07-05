<?php
namespace Agere\Domain\Dto;

/**
 *
 * @author Serzh
 *        
 */
interface DtoAwareInterface {
	
	public function getDto();

	public function setDto(Dto $dto);
}