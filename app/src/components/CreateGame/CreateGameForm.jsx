import React, { useState } from "react"
import MainInfo from "./MainInfo"
import Rules from "./Rules"
import Confirmation from "./Confirmation"

const CreateGameForm = () => {
    const [page, setPage] = useState(0)
    const [formData, setFormData] = useState({
        title: "",
        description: "",
        rules: [],
    })
    const titles = ["Main Informations", "Rules", "Confirmation"]

    const getComponent = (page) => {
        switch (page) {
            case 0: return <MainInfo formData={formData} setFormData={setFormData}/>; break;
            case 1: return <Rules formData={formData} setFormData={setFormData}/>; break;
            case 2: return <Confirmation formData={formData}/>; break;
        }
    }

    const getNextButton = (page) => {
        if (page === titles.length - 1) {
            return (
                <button
                    className="btn btn-primary"
                    onClick={submitForm}
                >Create game</button>
            )
        } else {
            return (
                <button
                    className="btn btn-primary"
                    disabled={
                        page === 0 && (formData.title === "" || formData.description === "")
                    }
                    onClick={() => {setPage((currPage) => currPage + 1)}}
                >Next</button>
            )
        }
    }

    const submitForm = async () => {
        const response = await fetch("/api/games", {
            method: "POST",
            body: JSON.stringify({
                "title": formData.title,
                "description": formData.description,
                "rules": formData.rules,
            })
        })

        if (response.ok) {
            console.log("Game ajouté !");
            window.location.replace("/games")
        }
        else {
            console.log("Erreur lors de la création du Game")
        }
    }

    return (
        <>
            <div className="container">
                <div className="form-header">
                    <h2>{titles[page]}</h2>
                </div>
                <div className="form-body">{getComponent(page)}</div>
                <div className="form-footer">
                    <button className="btn btn-secondary"
                            disabled={page === 0}
                            onClick={() => {setPage((currPage) => currPage - 1)}}
                    >Back</button>
                    {getNextButton(page)}
                </div>
            </div>
        </>
    )
}

export default CreateGameForm