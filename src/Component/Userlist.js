import React, { useState, useEffect } from "react";
import { Link } from "react-router-dom";
import axios from "axios";

function Userlist() { 
    const [userData, setUserData] = useState([]);
    const [message, setMessage] = useState('');

    useEffect(() => {   
        getUserData();
    }, []);

    const getUserData = async () => {
        try {
            const response = await axios.get("http://localhost/reactwebapp/api/user.php");
            console.log("API Response:", response.data); // Log the response to inspect its structure
            // Adjust to match the "userdata" array in the PHP response
            if (response.data.userdata) {
                setUserData(response.data.userdata);
            } else {
                setUserData([]);
                setMessage("No data found.");
            }
        } catch (error) {
            console.error("Error fetching user data:", error);
            setMessage("Failed to load user data.");
        }
    };

    const handleDelete = async (id) => {
        try {
            const response = await axios.delete("http://localhost/reactwebapp/api/user.php/"+id);
            setMessage(response.data.success);
            getUserData(); // Refresh user data after deletion
        } catch (error) {
            console.error("Error deleting user:", error);
            setMessage("Error deleting user.");
        }
    };

    return (
        <div className="container">  
            <div className="row">
                <div className="col-md-10 mt-4">
                    <h5 className="mb-4">User List</h5> 
                    <p className="text-danger">{message}</p>                 
                    <table className="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">Sr.No</th>
                                <th scope="col">Username</th>
                                <th scope="col">Email</th>
                                <th scope="col">Status</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            {userData.length === 0 ? (
                                <tr>
                                    <td colSpan="5" className="text-center">No users found.</td>
                                </tr>
                            ) : (
                                userData.map((uData, index) => (
                                    <tr key={uData.id}>
                                        <td>{index + 1}</td>
                                        <td>{uData.username}</td>
                                        <td>{uData.email}</td>
                                        <td>{uData.status === 1 ? "Active" : "Inactive"}</td>
                                        <td>
                                            <Link to={`/edituser/${uData.id}`} className="btn btn-success mx-2">Edit</Link>
                                            <button className="btn btn-danger" onClick={() => handleDelete(uData.id)}>Delete</button>
                                        </td>
                                    </tr>
                                ))
                            )}
                        </tbody>
                    </table>  
                </div>
            </div>
        </div>
    );
}

export default Userlist;
