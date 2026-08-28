import asyncio
import websockets
from pynput import keyboard
import time

clients = set()
buffer = ""

last_key_time = 0
last_send = 0

# tuning values
MAX_GAP = 0.08   # RFID is FAST (80ms between chars)
DEBOUNCE = 0.3

async def handler(websocket):
    clients.add(websocket)
    try:
        await websocket.wait_closed()
    finally:
        clients.remove(websocket)

async def broadcast(data):
    if clients:
        await asyncio.gather(*[c.send(data) for c in clients])

def on_press(key):
    global buffer, last_key_time, last_send

    try:
        now = time.time()

        # detect slow typing vs RFID burst
        if last_key_time and (now - last_key_time > MAX_GAP):
            buffer = ""  # reset if user typing normally

        last_key_time = now

        if key == keyboard.Key.enter:

            uid = buffer.strip().replace("\r", "").replace("\n", "")
            buffer = ""

            if not uid:
                return

            # prevent double send
            if now - last_send < DEBOUNCE:
                return

            last_send = now

            print("RFID:", repr(uid))

            asyncio.run_coroutine_threadsafe(
                broadcast(uid),
                loop
            )

        else:
            try:
                buffer += key.char
            except:
                pass

    except:
        pass

async def main():
    global loop
    loop = asyncio.get_event_loop()

    server = await websockets.serve(handler, "0.0.0.0", 6789)

    print("RFID SERVER RUNNING")

    listener = keyboard.Listener(on_press=on_press)
    listener.start()

    await server.wait_closed()

asyncio.run(main())