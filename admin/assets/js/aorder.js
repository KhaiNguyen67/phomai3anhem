// Xử lý gửi ngầm lệnh cập nhật trạng thái đơn hàng mượt mà bằng Fetch API
document.querySelectorAll('.change-order-status').forEach(select => {
    select.addEventListener('change', function() {
        let orderId = this.getAttribute('data-id');
        let statusVal = this.value;

        let formData = new FormData();
        formData.append('update_status', true);
        formData.append('order_id', orderId);
        formData.append('status', statusVal);

        fetch('orders.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => { 
            if(data.status === 'success') {
                alert('Cập nhật trạng thái đơn hàng #' + orderId + ' thành công!'); 
            } 
        })
        .catch(err => console.error("Lỗi:", err));
    });
});