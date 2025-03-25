import React, { useState } from "react";

const Searchbar = ({ userId, setUserId, label }) => {
    const [search, setSearch] = useState("")
    const [suggestions, setSuggestions] = useState([])
    const [timeout, newTimeout] = useState(null)
    const [isLoading, setIsLoading] = useState(false)

    const fetchUsers = async (username) => {
        const url = '/api/search-user?username=' + username
        const response = await fetch(url)
        const data = await response.json()
        setSuggestions(data || [])
        setIsLoading(false)
    }

    // Handle search input with debounce
    const searchUser = (e) => {
        const username = e.target.value
        setSearch(username)
        if (timeout != null) {
            clearTimeout(timeout)
            newTimeout(null)
            setIsLoading(false)
        }
        if (username.length > 2) {
            setIsLoading(true)
            newTimeout(setTimeout(() => fetchUsers(username), 1000))
        }
    }

    const displaySuggestions = () => {
        if (isLoading) {
            return (
                <div className="spinner-border" role="status">
                    <span className="visually-hidden">Loading...</span>
                </div>
            )
        } else if (search.length < 3) {
            return (<div></div>)
        } else if (suggestions.length !== 0) {
            return (
                <>
                    {suggestions.map((suggestion, index) => {
                        if(suggestion['avatar'] === "")
                        {
                            alert("Erreur !!!")
                        }
                        return (
                            <button
                                key={index}
                                className="list-group-item list-group-item-action"
                                onClick={() => selectUser(suggestion['id'])}
                            >
                                <img
                                    alt={suggestion['displayName'] + "'s avatar"}
                                    src={suggestion['avatar']}
                                    className="rounded-circle border border-dark me-2"
                                />
                                {suggestion['displayName']} (@{suggestion['name']})
                            </button>
                        )
                    })}
                </>
            )
        } else {
            return (<div>User not found.</div>)
        }
    }

    const selectUser = (id) => {
        setUserId(id)
        setSearch("")
    }

    return (
        <div>
            <label htmlFor="basic-addon1">{label}</label>
            <div className="input-group mb-3" id="basic-addon1">
                <div className="input-group-prepend">
                    <span className="input-group-text" id="basic-addon1">@</span>
                </div>
                <input
                    id="basic-addon1"
                    className="form-control"
                    placeholder="Username"
                    aria-label="Username"
                    aria-describedby="basic-addon1"
                    type="text"
                    value={search}
                    onChange={searchUser}
                />
            </div>
            <div className="list-group">{displaySuggestions()}</div>
        </div>
    )
}

export default Searchbar