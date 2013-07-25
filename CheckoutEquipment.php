class CheckoutEquipment extends DBQueries
{
    public $records = array();
    public $record_count;

    function getActiveCheckoutJTable($sort, $start, $page_size) {
        $sql = "SELECT * FROM Buildings,Rooms,CheckoutEquipment WHERE Buildings.id = Rooms.building_id
                AND Rooms.id = CheckoutEquipment.room_id AND CheckoutEquipment.status = 'New'
                ORDER BY %s LIMIT %d, %d";
        $sql = sprintf($sql, $sort, $start, $page_size);
        $this->records = $this->selectQuery($sql,"");
        $this->getCheckoutCount('New');

        return $this->records;
    }
    function getCompletedCheckoutJTable($sort, $start, $page_size) {
        $sql = "SELECT * FROM Buildings,Rooms,CheckoutEquipment WHERE Buildings.id = Rooms.building_id
                AND Rooms.id = CheckoutEquipment.room_id AND CheckoutEquipment.status = 'Completed'
                ORDER BY %s LIMIT %d, %d";
        $sql = sprintf($sql, $sort, $start, $page_size);
        $this->records = $this->selectQuery($sql,"");

        if (isset($this->records['room_id'])) {
            $room_class = new Room($this->records['room_id']);

            $this->records['full_room'] = $room_class->prefix . " " . $room_class->room;
        }
        $this->getCheckoutCount('Completed');

        return $this->records;
    }
    function getCheckoutCount($status) {
        $search = array(
            'status' => $status
        );
        $sql = "SELECT COUNT(*) as num_records FROM CheckoutEquipment WHERE CheckoutEquipment.status = :status";
        $this->record_count = $this->selectQuery($sql,$search);
    }
    function changeCheckoutRecord($changes) {
        if (isset($changes['id'])) {
            $sql = "UPDATE CheckoutEquipment SET name = :name, email = :email, date_checked_out = :date_checked_out
                , return_date = :return_date, room_id = :room_id, status = :status WHERE id = :id";
        } else {
            $sql = "INSERT INTO CheckoutEquipment (name, email, date_checked_out, return_date, room_id, status)
                VALUES (:name, :email, :date_checked_out, :return_date, :room_id, 'New')";
        }
        $success = $this->insertUpdateDeleteQuery($sql,$changes);

        if ($success) {
            return $changes;
        } else {
            return false;
        }
    }
    function deleteCheckoutRecord($record_id) {
        $changes = array(
            'id' => $record_id
        );
        $sql = "DELETE FROM CheckoutEquipment WHERE id = :id";

        $success = $this->insertUpdateDeleteQuery($sql,$changes);
        if ($success) {
            return true;
        } else {
            return false;
        }
    }
    function updatePDF($file_name,$id) {
        $changes = array(
            'pdf'   => $file_name,
            'id'    => $id
        );
        $sql = "UPDATE CheckoutEquipment SET pdf = :pdf WHERE id = :id";
        $success = $this->insertUpdateDeleteQuery($sql,$changes);

        return $success;
    }
}
