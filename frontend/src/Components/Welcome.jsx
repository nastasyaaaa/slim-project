import React from "react";
import styles from './Welcome.module.css';

function Welcome() {
    return (
        <div className={styles.welcome} data-test="welcome">
            <h1>Auction</h1>
            <p>Be here soon.</p>
        </div>
    )
}

export default Welcome