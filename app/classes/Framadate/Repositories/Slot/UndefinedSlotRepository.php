<?php
namespace Framadate\Repositories\Slot;

class UndefinedSlotRepository extends AbstractSlotRepository {
	public function templateCode() {
		new Exception("");
	}
}
