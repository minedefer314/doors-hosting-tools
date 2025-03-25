import React, {useEffect, useState} from "react";

const Games = () => {
    const [games, setGames] = useState([]);

    const displayGames = () => {
        return games.map((game, key) => {
            return (
                <div className="col d-flex"  key={key}>
                    <a className="card card-body d-flex flex-column h-100 w-100 text-center min" href="/">
                        <h5 className="card-title flex-grow-1 d-flex align-items-center justify-content-center">
                            <b><u>{game["title"]}</u></b>
                        </h5>
                        <h6 className="card-subtitle mb-2 text-body-secondary">Card subtitle</h6>
                    </a>
                </div>
            )
        })
    }

    const fetchGames = () => {
        fetch("/api/games.json")
            .then(data => data.json())
            .then(data => setGames(data))
    }

    useEffect(() => {
        fetchGames()
    }, [])

    return (
        <>
            <div className="container container-md">
                <div className="row row-cols-4 gy-3 gx-3 mb-5 text-center">
                    {displayGames()}
                </div>
                <a className="btn btn-outline-primary" href="/create-game">
                Create a new game
                </a>
            </div>
        </>
    )
}
export default Games