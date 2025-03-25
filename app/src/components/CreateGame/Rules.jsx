import React from "react"
import { useState } from "react"

const Rules = ({formData, setFormData}) => {
    const [newRule, setNewRule] = useState("")

    const removeRule = (index) => {
        const updatedRules = formData.rules.filter((_, i) => i !== index)
        setFormData({...formData, rules: updatedRules})
    }

    const addRule = () => {
        if (newRule.trim() !== "") {
            const updatedRules = [...formData.rules, newRule]
            setFormData({...formData, rules: updatedRules})
            setNewRule("")
        }
    }

    return (
        <>
            <div className="mb-3">
                <ol className="list-group">
                    {formData.rules.map((rule, index) => (
                        <li key={index} className="list-group-item d-flex align-items-center justify-content-between">
                            <span>{rule}</span>
                            <button
                                className="btn btn-danger btn-sm"
                                onClick={() => removeRule(index)}
                            >−</button>
                        </li>
                    ))}
                    <li className="list-group-item d-flex">
                        <input
                            type="text"
                            className="form-control me-2"
                            placeholder="Example : No hiding"
                            value={newRule}
                            onChange={(e) => setNewRule(e.target.value)}
                        />
                        <button
                            className="btn btn-success"
                            onClick={() => addRule()}
                        >+</button>
                    </li>
                </ol>
            </div>
        </>
    )
}
export default Rules