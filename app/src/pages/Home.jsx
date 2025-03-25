import React, { useState } from "react";
import SearchBar from "../components/Searchbar"
import Chat from "../components/Chat/Chat"

const Home = () => {
    const [userId, setUserId] = useState("");
    return (
        <>
            <div className="container-xxl h-100">
                <h1 className="text-center">Hello!!!</h1>
                <div className="container-md w-50">
                    <SearchBar
                        userId={userId}
                        setUserId={setUserId}
                        label="Select a user"
                    />
                    <p>Selected user id : {userId}</p>
                </div>

                <Chat />
            </div>
        </>
    )
}
export default Home