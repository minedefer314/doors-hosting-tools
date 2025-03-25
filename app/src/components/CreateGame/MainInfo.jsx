import React from "react"
import TextEditor from "./TextEditor"

const MainInfo = ({formData, setFormData}) => {

    const setDescription = (newValue) => {
        setFormData({...formData, description: newValue});
    }
    return (
        <>
            <div className="mb-3">
                <label
                    htmlFor="game-title"
                    className="form-label"
                >Game title</label>
                <input
                    type="text"
                    className="form-control"
                    id="game-title"
                    placeholder="Example : 🔰 FLOOR 2 NO HIDE MATCH 🔰"
                    value={formData.title}
                    onChange={(e) => setFormData({...formData, title: e.target.value})}
                />
            </div>
            <div className="mb-3">
                <label
                    htmlFor="exampleFormControlTextarea1"
                    className="form-label"
                >Description</label>
                <TextEditor value={formData.description} setValue={setDescription} />
            </div>
        </>
    )
}
export default MainInfo