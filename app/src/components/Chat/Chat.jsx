import React, { useState, useEffect } from "react";

const Chat = () => {
    const [messages, setMessages] = useState([])
    const [message, setMessage] = useState("")

    const sendMessage = async () => {
        if(message.trim() === "") return
        const response = await fetch("/api/messages", {
            method: "POST",
            headers: {"content-type": "application/json"},
            body: JSON.stringify({"content": message.trim()})
        })

        if(response.ok){
            setMessage("")
            await updateMessages()
        } else {
            console.log("An error occured while trying to send the message")
        }
    }

    const updateMessages = async () => {
        const response = await fetch("/api/messages.json")
        setMessages([...await response.json()])
    }

    const displayMessages = () => {
        return (
            <>
                {messages.map((message, index) => (
                    <div
                        key={index}
                        className="mb-2 mt-2 p-3 rounded-5"
                        style={{backgroundColor: "white"}}
                    >
                        <a href={message.sender.profile}>
                            <img
                                height="25px"
                                className="rounded-circle border border-secondary me-1"
                                src={message.sender.picture}
                                alt={message.sender.displayName + "'s avatar"}
                            />
                            {message.sender.displayName}
                        </a>
                        <p className="h5 mb-0 text-break">{message.content}</p>
                    </div>
                ))}
            </>
        )
    }

    useEffect(() => {
        updateMessages()
        const interval = setInterval(updateMessages, 1000)

        return () => clearInterval(interval)
    }, [])

    return (
        <div
            style={{backgroundColor: "lightblue", height: "500px", width: "50%", border: "2px solid black", borderRadius: "10px"}}
        >
            <h3
                className="text-center m-0 align-middle"
                style={{maxHeight: "10%", height: "10%"}}
            >Chat</h3>
            <div
                className="overflow-y-scroll list-group p-2"
                style={{backgroundColor: "lightblue", display: "flex", flexDirection: "column-reverse", maxHeight: "80%", height: "80%"}}
            >
                {displayMessages()}
            </div>
            <input
                style={{maxHeight: "10%", height: "10%"}}
                className="form-control"
                type="text"
                value={message}
                onChange={(e) => setMessage(e.target.value)}
                placeholder="Send a message"
                onKeyDown={(e) => {if (e.key === "Enter") sendMessage()}}
            />
        </div>
    )
}
export default Chat