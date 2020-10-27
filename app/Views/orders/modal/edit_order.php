<!-- Modal -->
<div class="modal fade" id="EditOrder" tabindex="-1" role="dialog" aria-labelledby="EditOrder" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="EditOrder">Ubah Pesanan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" >
      

                        
                    <div class="col-md-10">
                        <label >Market Place</label>
                      </div>
                      <div class="col-md-10 mb-3" align="center">
                                            <select class="form-control" name="marketplace" id="marketplace">
                      <option value="">Market Place</option>
                      <option value="LAZADA">LAZADA</option>
                      <option value="TOKOPEDIA">TOKOPEDIA</option>
                      <option value="SHOPEE">SHOPEE</option>
                      <option value="BUKALAPAK">BUKALAPAK</option>
                      <option value="OFFLINE">OFFLINE</option>
                      </select>
   
                                            <div class="invalid-feedback">
                                              
                                            </div>
                                        </div>
                     
                    
                    

                      <div class="col-md-10">
                      <label >Order ID</label>
                       </div>
                      
                        <div class="col-md-10 mb-3" >
                                            
                                            <input type="text" class="form-control" name="order_id" id="order_id" placeholder="Order ID" name="order_id" required>
                                            <div class="invalid-feedback">
                                              
                                            </div>
                                        </div>

                    
                                        <div class="col-md-10">
                                            <label >Nama Pelanggan</label>
                       </div>
                        <div class="col-md-10 mb-3" >
                                            <input type="text" class="form-control" id="name" placeholder="Masukan nama pelanggan" value="" name="name" required>
                                            <div class="valid-feedback">
                                                Looks good!
                                            </div>
                                        </div>