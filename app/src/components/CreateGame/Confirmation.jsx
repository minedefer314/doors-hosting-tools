import React from "react"

const Confirmation = ({formData}) => {
    const displayTitle = () => {
        return (
            <h1>{formData.title}</h1>
        )
    }

    const displayDescription = () => {
        return (
            <div dangerouslySetInnerHTML={{__html: formData.description}}/>
        )
    }

    const displayRules = () => {
        return (
            <>
                <h2>Rules</h2>
                <ul>
                    {formData.rules.map((rule, key) => {
                        return (
                            <li key={key}>{rule}</li>
                        )
                    })}
                </ul>
            </>
        )
    }

    return (
        <div>
            {displayTitle()}
            {displayDescription()}
            {displayRules()}
        </div>
    )
}
export default Confirmation