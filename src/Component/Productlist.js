import React, { useState, useEffect } from "react";
import { Link } from "react-router-dom";
import axios from "axios";

function Productlist() { 
  const [product, setProduct] = useState([]);
  const [message, setMessage] = useState('');


  useEffect(() => {
    
    getProduct();
  }, []);

  const getProduct = () => {
    fetch("http://localhost/reactwebapp/api/product.php")
      .then(res => res.json())
      .then(data => setProduct(data))
      .catch(error => console.log(error));
  };
  
 



  const handleDelete = async (id) => {
    try {
        const response = await axios.delete("http://localhost/reactwebapp/api/product.php/"+id);
        setMessage(response.data.success);
        getProduct(); // Refresh user data after deletion
    } catch (error) {
        console.error("Error deleting user:", error);
        setMessage("Error deleting user.");
    }
};

  return (
    <React.Fragment>
      <div className="container container_overflow">
        <div className="row">
          <div className="col-md-10 mt-4">
            <h5 className="mb-4">Product List</h5> 
            <table className="table table-bordered">
              <thead>
                <tr>
                  <th scope="col">Sr.No</th>
                  <th scope="col">Product Title</th>
                  <th scope="col">Product Price</th>
                  <th scope="col">Product Image</th>
                  <th scope="col">Product Status</th>
                  <th scope="col">Action</th>
                </tr>
              </thead>
              <tbody>
                {product.length === 0 ? (
                  <tr>
                    <td colSpan="6" className="text-center">No products found.</td>
                  </tr>
                ) : (
                  product.map((pdata, index) => (
                    <tr key={pdata.id}>
                      <td>{index + 1}</td>
                      <td>{pdata.ptitle}</td>
                      <td>{pdata.pprice}</td>
                      <td>
                        <img
                          src={`http://localhost/reactwebapp/images/${pdata.pimage}`}
                          alt="Product"
                          height={50}
                          width={90}
                        />
                      </td>
                      <td>{pdata.pstatus === 1 ? "Active" : "Inactive"}</td>
                      <td>
                        <Link to={`/editproductlist/${pdata.id}`} className="btn btn-success mx-2">Edit</Link>
                        <button className="btn btn-danger" onClick={() => handleDelete(pdata.id)}>Delete</button>
                        </td>
                    </tr>
                  ))
                )}
              </tbody>
            </table>  
          </div>
        </div>
      </div>
    </React.Fragment>
  );
}

export default Productlist;
