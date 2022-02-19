<template>
    <div class="wrapper">
        <div class="box">
            <div
                v-for="i in 42"
                :data-x="i - (7 * Math.trunc((i - 1) / 7))"
                :data-y="1 + Math.trunc((i - 1) / 7)"
                @click="clickOnBoard"
            >
                <span></span>
            </div>
        </div>
        <h1>{{ title }}</h1>
        <button class="btn btn-primary" @click="refreshPage" :disabled="!ended">Refresh Page</button>
    </div>
</template>

<script>
    export default {
        name: 'Game',
        data() {
            return {
                turnMe: true,
                colors: {
                    me: 'red',
                    ia: 'yellow',
                },
                board: {
                    1: [],
                    2: [],
                    3: [],
                    4: [],
                    5: [],
                    6: [],
                    7: [],
                },
                canClick: true,
                title: 'Game Started - Your Turn',
                ended: false,
                btnDisabled: 'disabled',
                BreakException: {},
                winnablePiece: {},
            }
        },
        methods: {
            clickOnBoard(e) {
                if (!this.canClick) {
                    return;
                }
                this.canClick = false;

                // Get clicked column
                const col = e.target.getAttribute('data-x');

                // Play your turn
                const ended = this.turn(this.colors.me, col);

                // Play opponent turn
                if (!ended) {
                    setTimeout(() => {
                        // TODO: IA's turn
                        const ia_col = Math.trunc(Math.random() * (7 - 1) + 1);
                        this.turn(this.colors.ia, ia_col);
                    }, Math.trunc(Math.random() * (1500 - 1000) + 1000));
                }
            },
            turn(side, col) {
                // Place piece
                const location_y = this.getIdOfTheLowestFreeLocation(col);
                document.querySelector('[data-x="'+col+'"][data-y="'+location_y+'"] > span').classList.add(side);
                this.board[col].push(side);

                try {
                    this.checkWin(side);

                    // Was your turn
                    if (side === this.colors.me) {
                        this.canClick = false;
                        this.title = 'Game Started - Opponent\'s Turn';
                    } else {
                        this.canClick = true;
                        this.title = 'Game Started - Your Turn';
                    }

                    return false;
                } catch (e) {
                    this.win(side);

                    return true;
                }
            },
            getIdOfTheLowestFreeLocation(col) {
                return 6 - this.board[col].length;
            },
            getPieceColor(div) {
                // If not div = out of wrapper
                if (!div) {
                    return null;
                }
                const span = div.querySelector('span');
                return span.classList.length > 0 ? span.classList[0] : null;
            },
            checkWin(side) {
                const divs = document.querySelectorAll('.box > div');

                divs.forEach(el => {
                    if (!this.ended) {
                        const span = el.querySelector('span');
                        const color = span.classList.length > 0 ? span.classList[0] : null;
                        const x = parseInt(el.getAttribute('data-x'));
                        const y = parseInt(el.getAttribute('data-y'));

                        // We got one piece of the desired color
                        if (color === side) {
                            let rr = 0;
                            let br = 0;
                            let bb = 0;
                            let bl = 0;

                            for (let i = 1 ; i <= 3 ; i++) {
                                // Right
                                this.getPieceColor(document.querySelector('[data-x="'+(x + i)+'"][data-y="'+y+'"]')) === side ? rr++ : '';

                                // Bottom Right
                                this.getPieceColor(document.querySelector('[data-x="'+(x + i)+'"][data-y="'+(y + i)+'"]')) === side ? br++ : '';

                                // Bottom
                                this.getPieceColor(document.querySelector('[data-x="'+x+'"][data-y="'+(y + i)+'"]')) === side ? bb++ : '';

                                // Bottom Left
                                this.getPieceColor(document.querySelector('[data-x="'+(x - i)+'"][data-y="'+(y + i)+'"]')) === side ? bl++ : '';
                            }

                            if (rr === 3) {
                                this.ended = true;
                                this.winnablePiece = {
                                    x: x,
                                    y: y,
                                    direction: 'rr'
                                }
                            }
                            else if (br === 3) {
                                this.ended = true;
                                this.winnablePiece = {
                                    x: x,
                                    y: y,
                                    direction: 'br'
                                }
                            }
                            else if (bb === 3) {
                                this.ended = true;
                                this.winnablePiece = {
                                    x: x,
                                    y: y,
                                    direction: 'bb'
                                }
                            }
                            else if (bl === 3) {
                                this.ended = true;
                                this.winnablePiece = {
                                    x: x,
                                    y: y,
                                    direction: 'bl'
                                }
                            }
                        }

                        if (this.ended) {
                            throw this.BreakException;
                        }
                    }
                });
            },
            win(side) {
                this.canClick = false;

                let title = 'Opponent';
                if (side === this.colors.me) {
                    title = 'You'
                }

                const spans = [];
                for (let i = 0 ; i <= 3 ; i++) {
                    if (this.winnablePiece.direction === 'rr')
                        spans.push(document.querySelector('[data-x="'+(this.winnablePiece.x + i)+'"][data-y="'+this.winnablePiece.y+'"] > span'));

                    else if (this.winnablePiece.direction === 'br')
                        spans.push(document.querySelector('[data-x="'+(this.winnablePiece.x + i)+'"][data-y="'+(this.winnablePiece.y + i)+'"] > span'));

                    else if (this.winnablePiece.direction === 'bb')
                        spans.push(document.querySelector('[data-x="'+this.winnablePiece.x+'"][data-y="'+(this.winnablePiece.y + i)+'"] > span'));

                    else if (this.winnablePiece.direction === 'bl')
                        spans.push(document.querySelector('[data-x="'+(this.winnablePiece.x - i)+'"][data-y="'+(this.winnablePiece.y + i)+'"] > span'));
                }
                let i = 0;
                setTimeout(() => {
                    const interval = setInterval(() => {
                        spans[i++].classList.add('animate');

                        if (i > 3) {
                            clearInterval(interval);
                        }
                    }, 200);
                }, 1000);

                this.title = 'Game Ended - ' + title + ' won the game !' ;
            },
            refreshPage() {
                window.location.reload();
            }
        }
    }
</script>

<style lang="scss" scoped>
    .wrapper {
        position: absolute;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;

        background: #263238;
        color: #fff;

        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
    }

    .box {
        display: grid;
        grid-template-columns: repeat(7, 80px);
        grid-gap: 20px;
        grid-template-rows: repeat(6, 80px);

        background: cornflowerblue;
        padding: 20px;

        margin-bottom: 5vh;

        div {
            width: 80px;
            border-radius: 50%;

            background: #fff;

            span {
                display: block;
                height: 100%;
                width: 100%;
                border-radius: 50%;
                pointer-events: none;

                transform: translateY(-1000px);

                transition: transform 0.4s ease-in-out;

                &.red {
                    transform: translateY(0);
                    background: red;
                }
                &.yellow {
                    transform: translateY(0);
                    background: yellow;
                }

                &.animate {
                    animation: scaleSpan 1s ease-in-out alternate infinite;
                }

                @keyframes scaleSpan {
                    from {
                        transform: scale(1);
                    }
                    to {
                        transform: scale(1.1);
                    }
                }
            }
        }
    }
</style>
